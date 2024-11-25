const express = require('express');
const router = express.Router();

// Import the necessary logic
const authMiddleware = require('../middleware/auth');
const AddressFactory = require('../utils/AddressFactory');

// Define routes
router.get('/generate/:crypto', authMiddleware, (req, res) => {
    const { crypto } = req.params; // Get the cryptocurrency type from the URL parameter
    try {
        const generator = AddressFactory.getAddressGenerator(crypto); // Get the appropriate generator
        const { address, privateKey } = generator.generateAddress(); // Generate the address and private key
        res.json({ address, privateKey }); // Respond with the generated data
    } catch (error) {
        res.status(400).json({ error: error.message }); // Handle error if something goes wrong
    }
});

module.exports = router;
