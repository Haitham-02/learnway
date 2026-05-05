-- Create admin user with the correct bcrypt hash
DELETE FROM users WHERE email = 'admin@learnway.local';

INSERT INTO users (role_id, email, password_hash, first_name, last_name, is_active, created_at) 
VALUES (1, 'admin@learnway.local', '$2y$13$hsO0pVenz21PEjs8XKvJEemLD20PeMYXOX2mvb0ZF/Er3n7jn3p1m', 'Admin', 'User', 1, NOW());

-- Verify user was created
SELECT id, email, first_name, last_name, role_id, is_active FROM users WHERE email = 'admin@learnway.local';
