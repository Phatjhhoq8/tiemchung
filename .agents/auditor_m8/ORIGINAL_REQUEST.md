## 2026-08-01T03:35:30Z
<USER_REQUEST>
You are the Forensic Auditor for Milestone M8 (FEFO Inventory Lots & Stock Reservation, R4, Ponytail Style).
Your working directory is /home/hongphuoc/Desktop/thue/.agents/auditor_m8. Create this directory if it does not exist.

Your task:
1. Execute a forensic integrity audit on Milestone M8 code changes.
2. Check:
   - Genuine implementation of FEFO allocation algorithm prioritizing nearest expiration date (`expires_at ASC`) and excluding recalled/quarantined/expired lots.
   - Genuine pessimistic row locking `lockForUpdate()` in `FefoInventoryService`.
   - Zero hardcoded test outputs, facade mocks, or bypassed verification routines.
   - Dynamic database stock movement recording (`reservation`, `release`, `deduction`).
3. Execute target test command:
   `/opt/lampp/bin/php ./vendor/bin/phpunit tests/Feature/FefoInventoryStockReservationTest.php`
4. Write forensic audit report to `/home/hongphuoc/Desktop/thue/.agents/auditor_m8/handoff.md` following standard format (Observation, Logic Chain, Caveats, Conclusion, Verdict).
5. Send completion message with your definitive verdict (CLEAN or INTEGRITY VIOLATION) to parent.
</USER_REQUEST>
