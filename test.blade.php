
mysql> SELECT * FROM vw_available_phones;
+----+---------+-----------------+--------+------------------+-----------------+----------------+---------------+------------------+---------------------+
| id | brand   | model           | color  | storage_capacity | imei            | purchase_price | selling_price | potential_profit | received_at         |
+----+---------+-----------------+--------+------------------+-----------------+----------------+---------------+------------------+---------------------+
|  2 | Apple   | iPhone 13       | Blue   | 128GB            | 356789000000002 |      700000.00 |     850000.00 |        150000.00 | 2026-06-03 22:08:14 |
|  1 | Apple   | iPhone 14       | Black  | 128GB            | 356789000000001 |      800000.00 |     950000.00 |        150000.00 | 2026-06-03 22:08:14 |
|  9 | Apple   | iPhone 14       | Blue   | 1223             | 4342423421      |      120000.00 |     200000.00 |         80000.00 | 2026-06-04 10:19:55 |
|  6 | Apple   | Iphone 15proMax | Silver | 11               | 1110099112      |         100.00 |     100000.00 |         99900.00 | 2026-06-03 23:06:16 |
|  8 | Apple   | Iphone 15proMax | Silver | 122              | 3233242312      |      120000.00 |     200000.00 |         80000.00 | 2026-06-04 09:51:23 |
|  3 | Samsung | Galaxy S23      | Green  | 256GB            | 356789000000003 |      900000.00 |    1100000.00 |        200000.00 | 2026-06-03 22:08:14 |
|  5 | Tecno   | Tecno Camon 20  | Gold   | 128GB            | 356789000000005 |      350000.00 |     450000.00 |        100000.00 | 2026-06-03 22:08:14 |
|  4 | Xiaomi  | Redmi Note 13   | Black  | 128GB            | 356789000000004 |      400000.00 |     520000.00 |        120000.00 | 2026-06-03 22:08:14 |
+----+---------+-----------------+--------+------------------+-----------------+----------------+---------------+------------------+---------------------+
8 rows in set (0.00 sec)

mysql> SELECT * FROM vw_stock_summary;
+---------+----------------+-------+---------------+---------------------+--------------+
| brand   | model          | color | current_stock | low_stock_threshold | stock_status |
+---------+----------------+-------+---------------+---------------------+--------------+
| Apple   | iPhone 14      | Black |             5 |                   2 | OK           |
| Samsung | Galaxy S23     | Green |             3 |                   2 | OK           |
| Tecno   | Tecno Camon 20 | Gold  |            10 |                   2 | OK           |
| Xiaomi  | Redmi Note 13  | Black |             8 |                   2 | OK           |
+---------+----------------+-------+---------------+---------------------+--------------+
4 rows in set (0.00 sec)

mysql> SELECT * FROM vw_sold_phones;
Empty set (0.00 sec)

mysql>
mysql> SELECT * FROM vw_sales_overview;
+---------+---------------------+-----------------------+----------------+--------------+-----------------+--------------+-------------+-------------+----------------+----------------+--------------+
| sale_id | sale_date           | customer_name         | customer_phone | total_amount | discount_amount | final_amount | amount_paid | amount_due  | payment_option | is_installment | served_by    |
+---------+---------------------+-----------------------+----------------+--------------+-----------------+--------------+-------------+-------------+----------------+----------------+--------------+
|       8 | 2026-06-04 09:48:04 | HAPPINESS LEMSI MKINI | 0754240750     |  85000000.00 |            0.00 |  85000000.00 |        0.00 | 85000000.00 | NULL           |              0 | Kelvin Stony |
|       7 | 2026-06-04 09:36:50 | kelvin stony dickson  | +255784252900  |  11400000.00 |            0.00 |  11400000.00 |        0.00 | 11400000.00 | NULL           |              0 | Kelvin Stony |
|       6 | 2026-06-04 00:23:53 | john francis          |                |    228000.00 |            0.00 |    228000.00 |        0.00 |   228000.00 | NULL           |              0 | Kelvin Stony |
|       5 | 2026-06-04 00:18:06 | john francis          | +255754240753  |  52000000.00 |            0.00 |  52000000.00 |        0.00 | 52000000.00 | NULL           |              0 | Kelvin Stony |
|       4 | 2026-06-04 00:13:31 | Stony Nzunda          | 0754240753     |    850000.00 |            0.00 |    850000.00 |        0.00 |        0.00 | NULL           |              0 | Kelvin Stony |
|       3 | 2026-06-03 23:53:10 | john francis          | +255784252900  |     10000.00 |            0.00 |     10000.00 |    20000.00 |   -10000.00 | NULL           |              0 | Kelvin Stony |
|       1 | 2026-06-03 22:10:12 | Ramadhan Mpangule     | 255711111111   |    950000.00 |        50000.00 |    900000.00 |   900000.00 |        0.00 | cash           |              0 | Kelvin Stony |
|       2 | 2026-06-03 22:10:12 | Patricia Munishi      | 255722222222   |   1100000.00 |       100000.00 |   1000000.00 |   500000.00 |   500000.00 | installment    |              1 | Kelvin Stony |
+---------+---------------------+-----------------------+----------------+--------------+-----------------+--------------+-------------+-------------+----------------+----------------+--------------+
8 rows in set (0.00 sec)

mysql> SELECT * FROM vw_sale_items_detail;
+----+---------+-----------------------+---------------------+---------+---------------+-----------+----------+------------+-----------+--------------+
| id | sale_id | customer_name         | sale_date           | brand   | item_name     | item_type | quantity | unit_price | unit_cost | gross_profit |
+----+---------+-----------------------+---------------------+---------+---------------+-----------+----------+------------+-----------+--------------+
|  7 |       8 | HAPPINESS LEMSI MKINI | 2026-06-04 09:48:04 | Apple   | iPhone 13     | Phone     |      100 |  850000.00 |      NULL |  85000000.00 |
|  6 |       7 | kelvin stony dickson  | 2026-06-04 09:36:50 | Apple   | iPhone 14     | Phone     |       12 |  950000.00 |      NULL |  11400000.00 |
|  5 |       6 | john francis          | 2026-06-04 00:23:53 | Generic | Phone Case    | Accessory |       19 |   12000.00 |      NULL |    228000.00 |
|  4 |       5 | john francis          | 2026-06-04 00:18:06 | Xiaomi  | Redmi Note 13 | Phone     |      100 |  520000.00 |      NULL |  52000000.00 |
|  3 |       4 | Stony Nzunda          | 2026-06-04 00:13:31 | Apple   | iPhone 13     | Phone     |        1 |  850000.00 |      NULL |    850000.00 |
|  1 |       1 | Ramadhan Mpangule     | 2026-06-03 22:10:12 | Apple   | iPhone 14     | Phone     |        1 |  950000.00 | 800000.00 |    150000.00 |
|  2 |       2 | Patricia Munishi      | 2026-06-03 22:10:12 | Samsung | Galaxy S23    | Phone     |        1 | 1100000.00 | 900000.00 |    200000.00 |
+----+---------+-----------------------+---------------------+---------+---------------+-----------+----------+------------+-----------+--------------+
7 rows in set (0.01 sec)

mysql> SELECT * FROM vw_daily_sales;
+------------+------------------+---------------+-----------------+--------------+-----------------+-------------------+
| sale_day   | num_transactions | gross_revenue | total_discounts | net_revenue  | total_collected | total_outstanding |
+------------+------------------+---------------+-----------------+--------------+-----------------+-------------------+
| 2026-06-04 |                5 |  149478000.00 |            0.00 | 149478000.00 |            0.00 |      148628000.00 |
| 2026-06-03 |                3 |    2060000.00 |       150000.00 |   1910000.00 |      1420000.00 |         490000.00 |
+------------+------------------+---------------+-----------------+--------------+-----------------+-------------------+
2 rows in set (0.00 sec)

mysql> SELECT * FROM vw_outstanding_balances;
+---------+-----------------------+----------------+----------------+--------------+-------------+-------------+---------------------+----------------+
| sale_id | customer_name         | customer_phone | customer_email | final_amount | amount_paid | amount_due  | sale_date           | is_installment |
+---------+-----------------------+----------------+----------------+--------------+-------------+-------------+---------------------+----------------+
|       8 | HAPPINESS LEMSI MKINI | 0754240750     | NULL           |  85000000.00 |        0.00 | 85000000.00 | 2026-06-04 09:48:04 |              0 |
|       5 | john francis          | +255754240753  | NULL           |  52000000.00 |        0.00 | 52000000.00 | 2026-06-04 00:18:06 |              0 |
|       7 | kelvin stony dickson  | +255784252900  | NULL           |  11400000.00 |        0.00 | 11400000.00 | 2026-06-04 09:36:50 |              0 |
|       2 | Patricia Munishi      | 255722222222   | mary@gmail.com |   1000000.00 |   500000.00 |   500000.00 | 2026-06-03 22:10:12 |              1 |
|       6 | john francis          |                | NULL           |    228000.00 |        0.00 |   228000.00 | 2026-06-04 00:23:53 |              0 |
+---------+-----------------------+----------------+----------------+--------------+-------------+-------------+---------------------+----------------+
5 rows in set (0.00 sec)

mysql>
mysql> SELECT * FROM vw_installment_summary;
+---------+------------------+----------------+------------+--------------------+--------------------+------------+--------+------------+-------------------+
| plan_id | customer_name    | customer_phone | sale_total | total_installments | installment_amount | start_date | status | total_paid | balance_remaining |
+---------+------------------+----------------+------------+--------------------+--------------------+------------+--------+------------+-------------------+
|       1 | Patricia Munishi | 255722222222   | 1000000.00 |                  5 |          200000.00 | 2026-06-03 | active |  400000.00 |         600000.00 |
+---------+------------------+----------------+------------+--------------------+--------------------+------------+--------+------------+-------------------+
1 row in set (0.00 sec)

mysql> SELECT * FROM vw_installment_payment_history;
+------------+---------+------------------+----------------+---------------------+-------------+-----------------+-------------+
| payment_id | plan_id | customer_name    | customer_phone | payment_date        | amount_paid | expected_amount | plan_status |
+------------+---------+------------------+----------------+---------------------+-------------+-----------------+-------------+
|          3 |       1 | Patricia Munishi | 255722222222   | 2026-06-03 22:12:26 |   200000.00 |       200000.00 | active      |
|          4 |       1 | Patricia Munishi | 255722222222   | 2026-06-03 22:12:26 |   200000.00 |       200000.00 | active      |
+------------+---------+------------------+----------------+---------------------+-------------+-----------------+-------------+
2 rows in set (0.01 sec)

mysql>
mysql> SELECT * FROM vw_accessories_stock;
+----+-------------------+---------+------------+----------------+---------------+-------------+----------------+---------------------+--------------+
| id | name              | brand   | category   | purchase_price | selling_price | unit_margin | stock_quantity | low_stock_threshold | stock_status |
+----+-------------------+---------+------------+----------------+---------------+-------------+----------------+---------------------+--------------+
|  2 | Samsung Earphones | Samsung | Audio      |       15000.00 |      25000.00 |    10000.00 |             15 |                   5 | In Stock     |
|  3 | Phone Case        | Generic | Case       |        5000.00 |      12000.00 |     7000.00 |             50 |                   5 | In Stock     |
|  1 | iPhone Charger    | Apple   | Charger    |       20000.00 |      35000.00 |    15000.00 |             20 |                   5 | In Stock     |
|  4 | Screen Protector  | Generic | Protection |        3000.00 |       8000.00 |     5000.00 |            100 |                   5 | In Stock     |
+----+-------------------+---------+------------+----------------+---------------+-------------+----------------+---------------------+--------------+
4 rows in set (0.00 sec)

mysql> SELECT * FROM vw_top_accessories;
+----+-------------------+------------+---------------+------------+---------------+
| id | name              | category   | selling_price | units_sold | total_revenue |
+----+-------------------+------------+---------------+------------+---------------+
|  3 | Phone Case        | Case       |      12000.00 |         19 |     228000.00 |
|  1 | iPhone Charger    | Charger    |      35000.00 |          0 |          0.00 |
|  2 | Samsung Earphones | Audio      |      25000.00 |          0 |          0.00 |
|  4 | Screen Protector  | Protection |       8000.00 |          0 |          0.00 |
+----+-------------------+------------+---------------+------------+---------------+
4 rows in set (0.00 sec)

mysql>
mysql> SELECT * FROM vw_expenses_summary;
+-----------+---------+-------------+-------------+
| category  | month   | num_entries | total_spent |
+-----------+---------+-------------+-------------+
| Rent      | 2026-06 |           1 |   300000.00 |
| Transport | 2026-06 |           1 |    50000.00 |
+-----------+---------+-------------+-------------+
2 rows in set (0.00 sec)

mysql> SELECT * FROM vw_receipt_summary;
+----------------+------------+-------------------+----------------+---------------------+------------+------+-----------+------------+----------------+---------+
| receipt_number | receipt_id | customer_name     | customer_phone | sale_date           | subtotal   | tax  | discount  | total      | payment_method | status  |
+----------------+------------+-------------------+----------------+---------------------+------------+------+-----------+------------+----------------+---------+
| R-3            |          3 | john francis      | +255784252900  | 2026-06-03 23:53:10 |   10000.00 | NULL |      NULL |   10000.00 | Cash           | paid    |
| RCP-1001       |          1 | Ramadhan Mpangule | 255711111111   | 2026-06-03 22:10:12 |  950000.00 | 0.00 |  50000.00 |  900000.00 | cash           | paid    |
| RCP-1002       |          2 | Patricia Munishi  | 255722222222   | 2026-06-03 22:10:12 | 1100000.00 | 0.00 | 100000.00 | 1000000.00 | installment    | partial |
+----------------+------------+-------------------+----------------+---------------------+------------+------+-----------+------------+----------------+---------+
3 rows in set (0.00 sec)

mysql>
mysql> SELECT * FROM vw_user_roles;
+---------+--------------+------------------------+--------------+-------+
| user_id | name         | email                  | phone_number | role  |
+---------+--------------+------------------------+--------------+-------+
|       1 | Kelvin Stony | kelvinstony9@gmail.com | 255700000001 | admin |
|       2 | Sales Staff  | staff@yoga.com         | 255700000002 | staff |
+---------+--------------+------------------------+--------------+-------+
2 rows in set (0.00 sec)

mysql> SELECT * FROM vw_user_permissions;
+---------+--------------+------------------------+----------------+--------------+
| user_id | name         | email                  | permission     | grant_source |
+---------+--------------+------------------------+----------------+--------------+
|       1 | Kelvin Stony | kelvinstony9@gmail.com | create_sales   | via role     |
|       1 | Kelvin Stony | kelvinstony9@gmail.com | edit_stock     | via role     |
|       1 | Kelvin Stony | kelvinstony9@gmail.com | manage_users   | via role     |
|       1 | Kelvin Stony | kelvinstony9@gmail.com | view_inventory | via role     |
|       1 | Kelvin Stony | kelvinstony9@gmail.com | view_reports   | via role     |
|       2 | Sales Staff  | staff@yoga.com         | create_sales   | via role     |
|       2 | Sales Staff  | staff@yoga.com         | view_inventory | via role     |
+---------+--------------+------------------------+----------------+--------------+
7 rows in set (0.00 sec)

mysql> SELECT * FROM vw_staff_sales_performance;





mysql> INSERT INTO phones (imei, model, brand_id, color, storage_capacity, purchase_price, selling_price, status)
 VALUES
 ('356789000000001', 'iPhone 14', 1, 'Black', '128GB', 800000, 950000, 'available'),
 ('356789000000002', 'iPhone 13', 1, 'Blue', '128GB', 700000, 850000, 'available'),
 ('356789000000003', 'Galaxy S23', 2, 'Green', '256GB', 900000, 1100000, 'available'),
 ('356789000000004', 'Redmi Note 13', 3, 'Black', '128GB', 400000, 520000, 'available'),
 ('356789000000005', 'Tecno Camon 20', 4, 'Gold', '128GB', 350000, 450000, 'available');
Query OK, 5 rows affected (0.01 sec)
Records: 5  Duplicates: 0  Warnings: 0

mysql> INSERT INTO accessories (name, brand, category, purchase_price, selling_price, stock_quantity)
 VALUES
 ('iPhone Charger', 'Apple', 'Charger', 20000, 35000, 20),
 ('Samsung Earphones', 'Samsung', 'Audio', 15000, 25000, 15),
 ('Phone Case', 'Generic', 'Case', 5000, 12000, 50),
 ('Screen Protector', 'Generic', 'Protection', 3000, 8000, 100);
Query OK, 4 rows affected (0.01 sec)
Records: 4  Duplicates: 0  Warnings: 0

mysql> INSERT INTO users (name, email, password, phone_number)
 VALUES
 ('Kelvin Stony', 'kelvinstony9@gmail.com', '$2y$10$hashedpasswordadmin', '255700000001'),
 ('Sales Staff', 'staff@yoga.com', '$2y$10$hashedpasswordstaff', '255700000002');
Query OK, 2 rows affected (0.01 sec)
Records: 2  Duplicates: 0  Warnings: 0

mysql> INSERT INTO roles (name) VALUES
 ('admin'),
 ('sales_manager'),
 ('staff'),
 ('viewer');
Query OK, 4 rows affected (0.00 sec)
Records: 4  Duplicates: 0  Warnings: 0

mysql> INSERT INTO permissions (name) VALUES
 ('view_inventory'),
 ('create_sales'),
 ('edit_stock'),
 ('manage_users'),
 ('view_reports');
Query OK, 5 rows affected (0.00 sec)
Records: 5  Duplicates: 0  Warnings: 0

mysql> INSERT INTO user_roles (user_id, role_id) VALUES
 (1, 1), -- Kelvin = admin
 (2, 3); -- staff
Query OK, 2 rows affected (0.00 sec)
Records: 2  Duplicates: 0  Warnings: 0

mysql> INSERT INTO role_permissions (role_id, permission_id) VALUES
 (1,1),(1,2),(1,3),(1,4),(1,5),
 (2,1),(2,2),(2,3),(2,5),
 (3,1),(3,2),
 (4,1),(4,5);
Query OK, 13 rows affected (0.01 sec)
Records: 13  Duplicates: 0  Warnings: 0

mysql> INSERT INTO stock_levels (brand_id, model, color, current_stock, low_stock_threshold)
 VALUES
 (1, 'iPhone 14', 'Black', 5, 2),
 (2, 'Galaxy S23', 'Green', 3, 2),
 (3, 'Redmi Note 13', 'Black', 8, 2),
 (4, 'Tecno Camon 20', 'Gold', 10, 2);
Query OK, 4 rows affected (0.01 sec)
Records: 4  Duplicates: 0  Warnings: 0

mysql> INSERT INTO sales (
customer_name, customer_phone, customer_email,
total_amount, discount_amount, final_amount,
amount_paid, amount_due,
payment_option, is_installment, user_id)
 VALUES
 ('Ramadhan Mpangule', '255711111111', 'rama@gmail.com',
 950000, 50000, 900000,900000, 0,'cash', FALSE, 1),
('Patricia Munishi', '255722222222', 'mary@gmail.com',
1100000, 100000, 1000000,
500000, 500000,'installment', TRUE, 1);
Query OK, 2 rows affected (0.01 sec)
Records: 2  Duplicates: 0  Warnings: 0

mysql> INSERT INTO sale_items (sale_id, phone_id, unit_price, quantity, unit_cost)
 VALUES
 (1, 1, 950000, 1, 800000),
 (2, 3, 1100000, 1, 900000);
Query OK, 2 rows affected (0.01 sec)
Records: 2  Duplicates: 0  Warnings: 0

mysql> INSERT INTO sale_receipts (receipt_number, sale_id, subtotal, tax, discount, total, payment_method, status)
 VALUES
 ('RCP-1001', 1, 950000, 0, 50000, 900000, 'cash', 'paid'),
 ('RCP-1002', 2, 1100000, 0, 100000, 1000000, 'installment', 'partial');
Query OK, 2 rows affected (0.00 sec)
Records: 2  Duplicates: 0  Warnings: 0

mysql> INSERT INTO installment_payments (installment_plan_id, amount_paid)
 VALUES
 (1, 200000),
 (1, 200000);
ERROR 1452 (23000): Cannot add or update a child row: a foreign key constraint fails
(`phone_pos`.`installment_payments`, CONSTRAINT `installment_payments_ibfk_1` FOREIGN KEY
(`installment_plan_id`) REFERENCES `installment_plans` (`id`) ON DELETE CASCADE)
mysql> SELECT * FROM installment_plans;
Empty set (0.00 sec)

mysql> INSERT INTO installment_plans (sale_id, total_installments, installment_amount, start_date, status)
 VALUES
 (2, 5, 200000, '2026-06-03', 'active');
Query OK, 1 row affected (0.00 sec)

mysql> INSERT INTO installment_payments (installment_plan_id, amount_paid)
 VALUES
 (1, 200000),(1, 200000);
Query OK, 2 rows affected (0.00 sec)
Records: 2  Duplicates: 0  Warnings: 0

mysql> INSERT INTO expenses (category, amount, description, expense_date, user_id)
 VALUES
 ('Rent', 300000, 'Monthly shop rent', '2026-06-01', 1),
 ('Transport', 50000, 'Delivery fuel', '2026-06-02', 1);
Query OK, 2 rows affected (0.01 sec)
Records: 2  Duplicates: 0  Warnings: 0










mysql> SELECT * FROM phones;
+----+-----------------+-----------------+----------+--------+------------------+----------------+---------------+-----------+---------------------+
| id | imei            | model           | brand_id | color  | storage_capacity | purchase_price | selling_price | status    | received_at         |
+----+-----------------+-----------------+----------+--------+------------------+----------------+---------------+-----------+---------------------+
|  1 | 356789000000001 | iPhone 14       |        1 | Black  | 128GB            |      800000.00 |     950000.00 | available | 2026-06-03 22:08:14 |
|  2 | 356789000000002 | iPhone 13       |        1 | Blue   | 128GB            |      700000.00 |     850000.00 | available | 2026-06-03 22:08:14 |
|  3 | 356789000000003 | Galaxy S23      |        2 | Green  | 256GB            |      900000.00 |    1100000.00 | available | 2026-06-03 22:08:14 |
|  4 | 356789000000004 | Redmi Note 13   |        3 | Black  | 128GB            |      400000.00 |     520000.00 | available | 2026-06-03 22:08:14 |
|  5 | 356789000000005 | Tecno Camon 20  |        4 | Gold   | 128GB            |      350000.00 |     450000.00 | available | 2026-06-03 22:08:14 |
|  6 | 1110099112      | Iphone 15proMax |        1 | Silver | 11               |         100.00 |     100000.00 | available | 2026-06-03 23:06:16 |
|  8 | 3233242312      | Iphone 15proMax |        1 | Silver | 122              |      120000.00 |     200000.00 | available | 2026-06-04 09:51:23 |
|  9 | 4342423421      | iPhone 14       |        1 | Blue   | 1223             |      120000.00 |     200000.00 | available | 2026-06-04 10:19:55 |
+----+-----------------+-----------------+----------+--------+------------------+----------------+---------------+-----------+---------------------+
8 rows in set (0.00 sec)

mysql> SELECT * FROM accessories;
+----+-------------------+---------+------------+----------------+---------------+----------------+---------------------+---------------------+------------+
| id | name              | brand   | category   | purchase_price | selling_price | stock_quantity | low_stock_threshold | created_at          | updated_at |
+----+-------------------+---------+------------+----------------+---------------+----------------+---------------------+---------------------+------------+
|  1 | iPhone Charger    | Apple   | Charger    |       20000.00 |      35000.00 |             20 |                   5 | 2026-06-03 22:08:24 | NULL       |
|  2 | Samsung Earphones | Samsung | Audio      |       15000.00 |      25000.00 |             15 |                   5 | 2026-06-03 22:08:24 | NULL       |
|  3 | Phone Case        | Generic | Case       |        5000.00 |      12000.00 |             50 |                   5 | 2026-06-03 22:08:24 | NULL       |
|  4 | Screen Protector  | Generic | Protection |        3000.00 |       8000.00 |            100 |                   5 | 2026-06-03 22:08:24 | NULL       |
+----+-------------------+---------+------------+----------------+---------------+----------------+---------------------+---------------------+------------+
4 rows in set (0.00 sec)

mysql> SELECT * FROM users;
+----+--------------+------------------------+----------------------------+--------------+
| id | name         | email                  | password                   | phone_number |
+----+--------------+------------------------+----------------------------+--------------+
|  1 | Kelvin Stony | kelvinstony9@gmail.com | kelvin@2025                | 255700000001 |
|  2 | Sales Staff  | staff@yoga.com         | $2y$10$hashedpasswordstaff | 255700000002 |
+----+--------------+------------------------+----------------------------+--------------+
2 rows in set (0.00 sec)

mysql> SELECT * FROM roles;
+----+---------------+---------+
| id | name          | user_id |
+----+---------------+---------+
|  1 | admin         |    NULL |
|  2 | sales_manager |    NULL |
|  3 | staff         |    NULL |
|  4 | viewer        |    NULL |
+----+---------------+---------+
4 rows in set (0.00 sec)

mysql> SELECT * FROM permissions;
+----+----------------+
| id | name           |
+----+----------------+
|  2 | create_sales   |
|  3 | edit_stock     |
|  4 | manage_users   |
|  1 | view_inventory |
|  5 | view_reports   |
+----+----------------+
5 rows in set (0.00 sec)

mysql> SELECT * FROM user_roles;
+---------+---------+
| user_id | role_id |
+---------+---------+
|       1 |       1 |
|       2 |       3 |
+---------+---------+
2 rows in set (0.00 sec)

mysql> SELECT * FROM role_permissions;
+---------+---------------+
| role_id | permission_id |
+---------+---------------+
|       1 |             1 |
|       2 |             1 |
|       3 |             1 |
|       4 |             1 |
|       1 |             2 |
|       2 |             2 |
|       3 |             2 |
|       1 |             3 |
|       2 |             3 |
|       1 |             4 |
|       1 |             5 |
|       2 |             5 |
|       4 |             5 |
+---------+---------------+
13 rows in set (0.00 sec)

mysql> SELECT * FROM stock_levels;
+----+----------+----------------+-------+---------------+---------------------+
| id | brand_id | model          | color | current_stock | low_stock_threshold |
+----+----------+----------------+-------+---------------+---------------------+
|  1 |        1 | iPhone 14      | Black |             5 |                   2 |
|  2 |        2 | Galaxy S23     | Green |             3 |                   2 |
|  3 |        3 | Redmi Note 13  | Black |             8 |                   2 |
|  4 |        4 | Tecno Camon 20 | Gold  |            10 |                   2 |
+----+----------+----------------+-------+---------------+---------------------+
4 rows in set (0.00 sec)

mysql> SELECT * FROM sales;
+----+-----------------------+----------------+----------------+--------------+-----------------+--------------+-------------+-------------+----------------+---------------------+----------------+---------+
| id | customer_name         | customer_phone | customer_email | total_amount | discount_amount | final_amount | amount_paid | amount_due  | payment_option | sale_date           | is_installment | user_id |
+----+-----------------------+----------------+----------------+--------------+-----------------+--------------+-------------+-------------+----------------+---------------------+----------------+---------+
|  1 | Ramadhan Mpangule     | 255711111111   | rama@gmail.com |    950000.00 |        50000.00 |    900000.00 |   900000.00 |        0.00 | cash           | 2026-06-03 22:10:12 |              0 |       1 |
|  2 | Patricia Munishi      | 255722222222   | mary@gmail.com |   1100000.00 |       100000.00 |   1000000.00 |   500000.00 |   500000.00 | installment    | 2026-06-03 22:10:12 |              1 |       1 |
|  3 | john francis          | +255784252900  | NULL           |     10000.00 |            0.00 |     10000.00 |    20000.00 |   -10000.00 | NULL           | 2026-06-03 23:53:10 |              0 |       1 |
|  4 | Stony Nzunda          | 0754240753     | NULL           |    850000.00 |            0.00 |    850000.00 |        0.00 |        0.00 | NULL           | 2026-06-04 00:13:31 |              0 |       1 |
|  5 | john francis          | +255754240753  | NULL           |  52000000.00 |            0.00 |  52000000.00 |        0.00 | 52000000.00 | NULL           | 2026-06-04 00:18:06 |              0 |       1 |
|  6 | john francis          |                | NULL           |    228000.00 |            0.00 |    228000.00 |        0.00 |   228000.00 | NULL           | 2026-06-04 00:23:53 |              0 |       1 |
|  7 | kelvin stony dickson  | +255784252900  | NULL           |  11400000.00 |            0.00 |  11400000.00 |        0.00 | 11400000.00 | NULL           | 2026-06-04 09:36:50 |              0 |       1 |
|  8 | HAPPINESS LEMSI MKINI | 0754240750     | NULL           |  85000000.00 |            0.00 |  85000000.00 |        0.00 | 85000000.00 | NULL           | 2026-06-04 09:48:04 |              0 |       1 |
+----+-----------------------+----------------+----------------+--------------+-----------------+--------------+-------------+-------------+----------------+---------------------+----------------+---------+
8 rows in set (0.00 sec)

mysql> SELECT * FROM sale_items;
+----+---------+----------+--------------+------------+----------+-----------+
| id | sale_id | phone_id | accessory_id | unit_price | quantity | unit_cost |
+----+---------+----------+--------------+------------+----------+-----------+
|  1 |       1 |        1 |         NULL |  950000.00 |        1 | 800000.00 |
|  2 |       2 |        3 |         NULL | 1100000.00 |        1 | 900000.00 |
|  3 |       4 |        2 |         NULL |  850000.00 |        1 |      NULL |
|  4 |       5 |        4 |         NULL |  520000.00 |      100 |      NULL |
|  5 |       6 |     NULL |            3 |   12000.00 |       19 |      NULL |
|  6 |       7 |        1 |         NULL |  950000.00 |       12 |      NULL |
|  7 |       8 |        2 |         NULL |  850000.00 |      100 |      NULL |
+----+---------+----------+--------------+------------+----------+-----------+
7 rows in set (0.00 sec)

mysql> SELECT * FROM sale_receipts;
+----+----------------+---------+------------+------+-----------+------------+----------------+---------+
| id | receipt_number | sale_id | subtotal   | tax  | discount  | total      | payment_method | status  |
+----+----------------+---------+------------+------+-----------+------------+----------------+---------+
|  1 | RCP-1001       |       1 |  950000.00 | 0.00 |  50000.00 |  900000.00 | cash           | paid    |
|  2 | RCP-1002       |       2 | 1100000.00 | 0.00 | 100000.00 | 1000000.00 | installment    | partial |
|  3 | R-3            |       3 |   10000.00 | NULL |      NULL |   10000.00 | Cash           | paid    |
+----+----------------+---------+------------+------+-----------+------------+----------------+---------+
3 rows in set (0.01 sec)

mysql> SELECT * FROM installment_plans;
+----+---------+--------------------+--------------------+------------+--------+
| id | sale_id | total_installments | installment_amount | start_date | status |
+----+---------+--------------------+--------------------+------------+--------+
|  1 |       2 |                  5 |          200000.00 | 2026-06-03 | active |
+----+---------+--------------------+--------------------+------------+--------+
1 row in set (0.00 sec)

mysql> SELECT * FROM installment_payments;
+----+---------------------+---------------------+-------------+
| id | installment_plan_id | payment_date        | amount_paid |
+----+---------------------+---------------------+-------------+
|  3 |                   1 | 2026-06-03 22:12:26 |   200000.00 |
|  4 |                   1 | 2026-06-03 22:12:26 |   200000.00 |
+----+---------------------+---------------------+-------------+
2 rows in set (0.00 sec)

mysql> SELECT * FROM expenses;
+----+-----------+-----------+-------------------+--------------+---------+
| id | category  | amount    | description       | expense_date | user_id |
+----+-----------+-----------+-------------------+--------------+---------+
|  1 | Rent      | 300000.00 | Monthly shop rent | 2026-06-01   |       1 |
|  2 | Transport |  50000.00 | Delivery fuel     | 2026-06-02   |       1 |
+----+-----------+-----------+-------------------+--------------+---------+
2 rows in set (0.00 sec)

mysql>












