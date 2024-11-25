require('dotenv').config({ path: '../.env' });  // Ensure the path to the .env file is correct

const express = require('express');
const bodyParser = require('body-parser');
const app = express();

// Import routes
const addressRoutes = require('./routes/addressRoutes');

// Middleware
app.use(bodyParser.json()); // Allow reading JSON bodies

// Use routes for handling address generation
app.use('/api/addresses', addressRoutes);

// Start the server
app.listen(process.env.NODE_PORT, () => {
    console.log(`Node.js server running on port ${process.env.NODE_PORT}`);
});
