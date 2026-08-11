<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('point_transactions', function (Blueprint $table) {
            $table->json('metadata')->nullable()->after('note');
            $table->foreignId('reverses_transaction_id')->nullable()->after('metadata')->constrained('point_transactions')->nullOnDelete();
        });

        Schema::create('point_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('credit_transaction_id')->constrained('point_transactions')->cascadeOnDelete();
            $table->foreignId('debit_transaction_id')->constrained('point_transactions')->cascadeOnDelete();
            $table->bigInteger('points');
            $table->timestamps();

            $table->index('credit_transaction_id');
            $table->index('debit_transaction_id');
        });

        // Chạy Backfill allocation cho các giao dịch lịch sử
        $this->backfillAllocations();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_allocations');

        Schema::table('point_transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reverses_transaction_id');
            $table->dropColumn('metadata');
        });
    }

    /**
     * Thuật toán Backfill phân bổ điểm lịch sử
     */
    private function backfillAllocations(): void
    {
        // 1. Đối sánh reverses_transaction_id cho các giao dịch đảo điểm cũ dựa trên registration_id
        $refunds = \Illuminate\Support\Facades\DB::table('point_transactions')
            ->where('type', 'redeem_refund')
            ->whereNull('reverses_transaction_id')
            ->whereNotNull('registration_id')
            ->get();

        foreach ($refunds as $rf) {
            $redeem = \Illuminate\Support\Facades\DB::table('point_transactions')
                ->where('registration_id', $rf->registration_id)
                ->where('type', 'redeem')
                ->first();
            if ($redeem) {
                \Illuminate\Support\Facades\DB::table('point_transactions')
                    ->where('id', $rf->id)
                    ->update(['reverses_transaction_id' => $redeem->id]);
            }
        }

        $reversals = \Illuminate\Support\Facades\DB::table('point_transactions')
            ->where('type', 'earn_reversal')
            ->whereNull('reverses_transaction_id')
            ->whereNotNull('registration_id')
            ->get();

        foreach ($reversals as $rv) {
            $earn = \Illuminate\Support\Facades\DB::table('point_transactions')
                ->where('registration_id', $rv->registration_id)
                ->where('type', 'earn')
                ->first();
            if ($earn) {
                \Illuminate\Support\Facades\DB::table('point_transactions')
                    ->where('id', $rv->id)
                    ->update(['reverses_transaction_id' => $earn->id]);
            }
        }

        // 2. Chạy FIFO Backfill cho từng khách hàng
        $customerIds = \Illuminate\Support\Facades\DB::table('point_transactions')
            ->distinct()
            ->pluck('customer_id');

        foreach ($customerIds as $customerId) {
            // Lấy tất cả credit transactions (EARN, ADJUSTMENT > 0)
            $credits = \Illuminate\Support\Facades\DB::table('point_transactions')
                ->where('customer_id', $customerId)
                ->whereIn('type', ['earn', 'adjustment'])
                ->where('points', '>', 0)
                ->orderByRaw('expired_at IS NULL, expired_at ASC, id ASC')
                ->get()
                ->map(function ($c) {
                    $c->points_remaining = $c->points;
                    return $c;
                });

            // Lấy tất cả debit transactions (REDEEM, ADJUSTMENT < 0, EARN_REVERSAL)
            $debits = \Illuminate\Support\Facades\DB::table('point_transactions')
                ->where('customer_id', $customerId)
                ->where(function ($query) {
                    $query->whereIn('type', ['redeem', 'earn_reversal'])
                          ->orWhere(function ($q) {
                              $q->where('type', 'adjustment')->where('points', '<', 0);
                          });
                })
                ->orderBy('created_at', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            foreach ($debits as $d) {
                $needed = abs($d->points);

                // TRƯỜNG HỢP ĐẶC BIỆT: EARN_REVERSAL chỉ được tiêu điểm của EARN gốc của nó
                if ($d->type === 'earn_reversal' && $d->reverses_transaction_id) {
                    $originalEarn = $credits->firstWhere('id', $d->reverses_transaction_id);
                    if ($originalEarn && $originalEarn->points_remaining > 0) {
                        $alloc = min($originalEarn->points_remaining, $needed);
                        
                        \Illuminate\Support\Facades\DB::table('point_allocations')->insert([
                            'credit_transaction_id' => $originalEarn->id,
                            'debit_transaction_id' => $d->id,
                            'points' => $alloc,
                            'created_at' => $d->created_at ?: now(),
                            'updated_at' => $d->created_at ?: now(),
                        ]);

                        $originalEarn->points_remaining -= $alloc;
                        $needed -= $alloc;
                    }
                }

                // Nếu là REDEEM hoặc ADJUSTMENT < 0 (hoặc EARN_REVERSAL còn thiếu điểm để đảo)
                if ($needed > 0) {
                    foreach ($credits as $c) {
                        if ($needed <= 0) {
                            break;
                        }
                        if ($c->points_remaining <= 0) {
                            continue;
                        }

                        // Nếu là EARN_REVERSAL, không được lấy điểm từ credit lot khác (trừ khi không có cách nào khác)
                        if ($d->type === 'earn_reversal' && $c->id !== $d->reverses_transaction_id) {
                            continue;
                        }

                        $alloc = min($c->points_remaining, $needed);

                        \Illuminate\Support\Facades\DB::table('point_allocations')->insert([
                            'credit_transaction_id' => $c->id,
                            'debit_transaction_id' => $d->id,
                            'points' => $alloc,
                            'created_at' => $d->created_at ?: now(),
                            'updated_at' => $d->created_at ?: now(),
                        ]);

                        $c->points_remaining -= $alloc;
                        $needed -= $alloc;
                    }
                }
            }

            // 3. Khôi phục điểm cho các REDEEM_REFUND lịch sử
            $refundTxes = \Illuminate\Support\Facades\DB::table('point_transactions')
                ->where('customer_id', $customerId)
                ->where('type', 'redeem_refund')
                ->whereNotNull('reverses_transaction_id')
                ->orderBy('created_at', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            foreach ($refundTxes as $rf) {
                $redeemId = $rf->reverses_transaction_id;
                $refundAmount = abs($rf->points); // Số điểm hoàn trả (dương)

                // Tìm các allocations của giao dịch REDEEM gốc
                $allocations = \Illuminate\Support\Facades\DB::table('point_allocations')
                    ->where('debit_transaction_id', $redeemId)
                    ->orderBy('id', 'desc') // Hoàn trả theo thứ tự LIFO (lô bị trừ sau được trả lại trước)
                    ->get();

                foreach ($allocations as $alloc) {
                    if ($refundAmount <= 0) {
                        break;
                    }

                    $returnVal = min($alloc->points, $refundAmount);

                    if ($returnVal === $alloc->points) {
                        // Xóa allocation này
                        \Illuminate\Support\Facades\DB::table('point_allocations')
                            ->where('id', $alloc->id)
                            ->delete();
                    } else {
                        // Giảm số điểm phân bổ
                        \Illuminate\Support\Facades\DB::table('point_allocations')
                            ->where('id', $alloc->id)
                            ->decrement('points', $returnVal);
                    }

                    $refundAmount -= $returnVal;

                    // Cập nhật lại points_remaining trong bộ nhớ cache
                    $c = $credits->firstWhere('id', $alloc->credit_transaction_id);
                    if ($c) {
                        $c->points_remaining += $returnVal;
                    }
                }
            }
        }
    }
};
