-- =============================================
-- Performance Indexes for E-FMS
-- Run this on the Efms database to improve query performance.
-- These indexes target the most frequently queried columns
-- that currently lack indexes, causing full table scans.
-- =============================================

-- CRITICAL: UserLogin.ApiKey is queried on EVERY authenticated API request
-- Without this index, every request does a full table scan
ALTER TABLE `UserLogin` ADD INDEX `idx_userlogin_apikey` (`ApiKey`);

-- ListJob: Most queried table - needs indexes on all filter/join columns
ALTER TABLE `ListJob` ADD INDEX `idx_listjob_userid` (`UserID`);
ALTER TABLE `ListJob` ADD INDEX `idx_listjob_companyid` (`CompanyID`);
ALTER TABLE `ListJob` ADD INDEX `idx_listjob_status` (`Status`);
ALTER TABLE `ListJob` ADD INDEX `idx_listjob_customerid` (`CustomerID`);
ALTER TABLE `ListJob` ADD INDEX `idx_listjob_jobdate` (`JobDate`);
ALTER TABLE `ListJob` ADD INDEX `idx_listjob_createdby` (`CreatedBy`);

-- Composite index for the most common query pattern: jobs by company + status
ALTER TABLE `ListJob` ADD INDEX `idx_listjob_company_status` (`CompanyID`, `Status`);

-- ListJobDetail: Joined on ListJobID for every job detail lookup
ALTER TABLE `ListJobDetail` ADD INDEX `idx_listjobdetail_listjobid` (`ListJobID`);

-- RescheduledJob: Queried by JobID in loops (N+1 pattern)
ALTER TABLE `RescheduledJob` ADD INDEX `idx_rescheduledjob_jobid` (`JobID`);

-- HistoryCancelJob: Queried by JobID in loops (N+1 pattern)
ALTER TABLE `HistoryCancelJob` ADD INDEX `idx_historycanceljob_jobid` (`JobID`);

-- ListUser: Joined on ListCompanyID for company scoping
ALTER TABLE `ListUser` ADD INDEX `idx_listuser_listcompanyid` (`ListCompanyID`);
ALTER TABLE `ListUser` ADD INDEX `idx_listuser_userloginid` (`UserLoginID`);

-- Customer: Joined on ListCompanyID
ALTER TABLE `Customer` ADD INDEX `idx_customer_listcompanyid` (`ListCompanyID`);
