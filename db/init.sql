CREATE TABLE IF NOT EXISTS categories (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    display_order INT DEFAULT 0
);

CREATE TABLE IF NOT EXISTS menu_items (
    id SERIAL PRIMARY KEY,
    category_id INT REFERENCES categories(id),
    name VARCHAR(150) NOT NULL,
    description TEXT,
    price NUMERIC(10, 2) NOT NULL,
    is_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO categories (name, display_order) VALUES
('Starters', 1),
('Main Course', 2),
('Desserts', 3),
('Beverages', 4);

INSERT INTO menu_items (category_id, name, description, price) VALUES
(1, 'Garlic Bread', 'Toasted bread with garlic butter and herbs', 5.99),
(1, 'Caesar Salad', 'Romaine lettuce, croutons, parmesan, caesar dressing', 8.49),
(1, 'Soup of the Day', 'Chef special seasonal soup', 6.99),
(2, 'Grilled Chicken', 'Herb-marinated chicken breast with roasted vegetables', 16.99),
(2, 'Lamb Biryani', 'Aromatic basmati rice with tender lamb and spices', 18.99),
(2, 'Paneer Tikka Masala', 'Cottage cheese in rich tomato-cream curry', 14.99),
(2, 'Fish and Chips', 'Beer-battered cod with crispy fries and tartar sauce', 15.49),
(3, 'Tiramisu', 'Classic Italian coffee-flavored dessert', 9.99),
(3, 'Gulab Jamun', 'Deep-fried milk dumplings in rose sugar syrup', 7.49),
(4, 'Masala Chai', 'Spiced Indian tea with milk', 3.99),
(4, 'Fresh Lime Soda', 'Freshly squeezed lime with soda water', 4.49);

