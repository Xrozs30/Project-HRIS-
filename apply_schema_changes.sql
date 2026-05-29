SET FOREIGN_KEY_CHECKS=0;

-- 1. Drop constraints from employees table
ALTER TABLE employees DROP FOREIGN KEY employees_allowance_id_foreign;

-- 2. Drop constraints from transactional table
ALTER TABLE transactional DROP FOREIGN KEY transactional_allowance_id_foreign;

-- 3. Drop allowances table
DROP TABLE IF EXISTS allowances;

-- 4. Alter employees table (Drop columns, rename columns)
ALTER TABLE employees 
  DROP COLUMN allowance_id,
  DROP COLUMN allowance_type,
  CHANGE `employee_addres` `employee_address` text NULL,
  CHANGE `role` `employee_role` enum('hr','employee','owner') NULL DEFAULT 'employee';

-- 5. Alter transactional table (Drop allowance_id column)
ALTER TABLE transactional
  DROP COLUMN allowance_id;

SET FOREIGN_KEY_CHECKS=1;

SELECT 'Schema updated successfully!' AS result;
