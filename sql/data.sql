INSERT INTO
    `plat4m_store` (`name`, `address`)
VALUES
    ('Plat4m Test Store', NULL),
    ('Standard Liquor', '303 1st St. Los Altos CA 94022'),
    ('Lake Vista Liquors', NULL);


INSERT INTO
    `plat4m_user` (`name`, `email`, `password`, `store_id`, `user_type`, `email_verified`)
VALUES
    ('Admin', 'admin@gmail.com', 'a1d2m3i4n', NULL, 'superadmin', 'verified'),
    ('Druthi', 'druthi@gmail.com', '1234', 1, 'storeadmin', 'verified'),
    ('Abhyaan', 'abhyaan@gmail.com', '1234', 1, 'storeadmin', 'verified'),
    ('Anna', 'anna@t3stores.com', '1234', 1, 'storeadmin', 'verified'),
    ('Dyumani', 'dyumani@t3stores.com', '1234', 1, 'storeadmin', 'verified'),
    ('Rahul', 'rahul@t3stores.com', '1234', 1, 'storeadmin', 'verified'),
    ('Nanda', 'nanda@t3stores.com', '1234', 1, 'storeadmin', 'verified'),
    ('Vijaya', 'vijaya@t3stores.com', '1234', 1, 'storeadmin', 'verified'),
    ('Srinu', 'srinu@t3stores.com', '1234', 1, 'storeadmin', 'verified'),
    ('Tim', 'tim@t3stores.com', '1234', 1, 'storeadmin', 'verified'),
    ('Tom', 'tom@t3stores.com', '1234', 1, 'storeadmin', 'verified'),
    ('Paul', 'paul@t3stores.com', '1234', 1, 'storeadmin', 'verified'),
    ('Standard Liquor', 'standardliquor@gmail.com', '1234', 2, 'storeadmin', 'verified'),
    ('Sam', 'sam@gmail.com', '1234', 1, 'storeadmin', 'verified'),
    ('Lake Vista', 'lakevistaliquors@t3stores.com', '1133', 3, 'storeadmin', 'verified'),
    ('KP Teja', 'kpteja030393@gmail.com', '123456', 1, 'storeadmin', 'verified'),
    ('Jack West Stinson', 'jack1374west@gmail.com', '1473', 1, 'storeadmin', 'verified');