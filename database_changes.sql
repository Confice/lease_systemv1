-- =====================================================
-- DATABASE CHANGES REQUIRED
-- Run these SQL statements in your database
-- =====================================================

-- =====================================================
-- 1. CONTRACTS TABLE CHANGES
-- =====================================================

-- Step 1: Migrate existing data to customReason (if any exists)
-- This consolidates terminationReason/terCustomReason/expCustomReason into customReason
UPDATE contracts 
SET customReason = CASE
    WHEN terCustomReason IS NOT NULL AND terCustomReason != '' THEN terCustomReason
    WHEN expCustomReason IS NOT NULL AND expCustomReason != '' THEN expCustomReason
    WHEN terminationReason IS NOT NULL AND terminationReason != '' THEN terminationReason
    ELSE NULL
END
WHERE (terminationReason IS NOT NULL OR terCustomReason IS NOT NULL OR expCustomReason IS NOT NULL)
  AND customReason IS NULL;

-- Step 2: Drop old columns from contracts table
ALTER TABLE contracts 
DROP COLUMN IF EXISTS terminationReason,
DROP COLUMN IF EXISTS terCustomReason,
DROP COLUMN IF EXISTS expCustomReason;

-- Step 3: Add customReason column if it doesn't exist
ALTER TABLE contracts 
ADD COLUMN IF NOT EXISTS customReason TEXT NULL COMMENT 'Reason for terminating contract or rejecting renewal request';

-- =====================================================
-- 2. USERS TABLE CHANGES
-- =====================================================

-- Step 1: Update any existing 'Locked' status to 'Deactivated'
-- (You may want to review these records first)
UPDATE users 
SET userStatus = 'Deactivated' 
WHERE userStatus = 'Locked';

-- Step 2: Migrate deactivationReason to customReason if needed
-- This moves any existing deactivationReason data to customReason
UPDATE users 
SET customReason = deactivationReason 
WHERE deactivationReason IS NOT NULL 
  AND deactivationReason != '' 
  AND customReason IS NULL;

-- Step 3: Drop deactivationReason column
ALTER TABLE users 
DROP COLUMN IF EXISTS deactivationReason;

-- Step 4: Modify userStatus enum to remove 'Locked'
-- Note: MySQL doesn't support removing enum values directly, so we need to recreate it
ALTER TABLE users 
MODIFY COLUMN userStatus ENUM('Active', 'Pending', 'Deactivated') DEFAULT 'Pending';

-- Step 5: Change customReason from VARCHAR(150) to TEXT if needed
-- Check current column type first, then modify if necessary
ALTER TABLE users 
MODIFY COLUMN customReason TEXT NULL COMMENT 'Reason for deactivating account';

-- =====================================================
-- VERIFICATION QUERIES (Run these to verify changes)
-- =====================================================

-- Check contracts table structure
-- DESCRIBE contracts;

-- Check users table structure  
-- DESCRIBE users;

-- Check for any remaining 'Locked' status (should return 0 rows)
-- SELECT id, email, userStatus FROM users WHERE userStatus = 'Locked';

-- Check for any remaining old contract reason columns (should return 0 rows)
-- SELECT contractID FROM contracts WHERE terminationReason IS NOT NULL OR terCustomReason IS NOT NULL OR expCustomReason IS NOT NULL;

