UPDATE products SET title = 'PLA Low-Poly Poodle Ornament', filament_filter = 'L,E', last_updated = '2023-11-05 21:20:31' WHERE idproducts = 1;
UPDATE products SET active = 0, last_updated = '2023-11-05 21:21:20' WHERE idproducts = 3;
UPDATE products SET active = 0, last_updated = '2023-11-05 21:21:20' WHERE idproducts = 4;

INSERT INTO products(idproducts, sku, title, description, price, filament_usage, addl_cost, filament_filter, tags, categories, upc, wholesale_price, active, special_order, size_options, quality_options, wholesale_qty, discount_price, discount_start, discount_end, default_photo, date_added, last_updated) VALUES
(5, 'ALO-POO-PSN', 'PLA+ Low-Poly Poodle Ornament', '3D Printed Low-Poly Poodle Ornament. This model is made with PLA+ which is a stronger filament than PLA.', 15, 66, 0, 'P', '3D,animal,poodle,ornament,christmas,low-poly', 'Animals,Low-Poly,Ornaments', '', 10, 1, 0, NULL, NULL, 16, 10, '2023-10-06 00:00:00', '2023-10-31 23:59:59', 'https://images.kumpeapps.com/filament?swatch=default', '2023-10-05', '2023-11-05 21:10:45'),
(6, 'ALO-POO-KSN', 'Silk Low-Poly Poodle Ornament', '3D Printed Low-Poly Poodle Ornament. This model is made with Silk PLA which has a shine to it.', 15, 66, 0, 'K', '3D,animal,poodle,ornament,christmas,low-poly', 'Animals,Low-Poly,Ornaments', '', 10, 1, 0, NULL, NULL, 16, 10, '2023-10-06 00:00:00', '2023-10-31 23:59:59', 'https://images.kumpeapps.com/filament?swatch=default', '2023-10-05', '2023-11-05 21:10:45'),
(7, 'ALO-POO-SSN', 'Sparkle Low-Poly Poodle Ornament', '3D Printed Low-Poly Poodle Ornament. This model is made with Sparkle PLA which glitter like particles in the product.', 15, 66, 0, 'S', '3D,animal,poodle,ornament,christmas,low-poly', 'Animals,Low-Poly,Ornaments', '', 10, 1, 0, NULL, NULL, 16, 10, '2023-10-06 00:00:00', '2023-10-31 23:59:59', 'https://images.kumpeapps.com/filament?swatch=default', '2023-10-05', '2023-11-05 21:10:45'),
(8, 'ALO-POO-WSN', 'Glow Low-Poly Poodle Ornament', '3D Printed Low-Poly Poodle Ornament. This model is made with Glow In The Dark PLA.', 15, 66, 0, 'W', '3D,animal,poodle,ornament,christmas,low-poly', 'Animals,Low-Poly,Ornaments', '', 10, 1, 0, NULL, NULL, 16, 10, '2023-10-06 00:00:00', '2023-10-31 23:59:59', 'https://images.kumpeapps.com/filament?swatch=default', '2023-10-05', '2023-11-05 21:10:45'),
(9, 'ALO-POO-RSN', 'Rock Low-Poly Poodle Ornament', '3D Printed Low-Poly Poodle Ornament. This model is made with Rock PLA which has specks in it that give it a Rock look.', 15, 66, 0, 'R', '3D,animal,poodle,ornament,christmas,low-poly', 'Animals,Low-Poly,Ornaments', '', 10, 1, 0, NULL, NULL, 16, 10, '2023-10-06 00:00:00', '2023-10-31 23:59:59', 'https://images.kumpeapps.com/filament?swatch=default', '2023-10-05', '2023-11-05 21:10:45');


update stock set sku = concat(left(sku,8),left(swatch_id,1),right(sku,2)) where left(swatch_id,1) != 'E';
update orders__items set sku = concat(left(sku,8),substring(sku,13,1),right(sku,6)) where left(sku,3) = 'ALO' AND substring(sku,13,1) != 'E';