const jwt = require('jsonwebtoken');

const authMiddleware = (req, res, next) => {
    const token = req.headers['authorization']; // Get the token from the request headers

    // if (!token) {
    //     return res.status(403).json({ message: 'No token provided.' }); // If token is missing
    // }

    // Verify token
    jwt.verify(token, process.env.JWT_SECRET, (err, decoded) => {
   /*     if (err) {
            return res.status(401).json({ message: 'Invalid token.' }); // Invalid token
        }*/

        req.user = decoded; // Attach decoded user data to the request object
        next(); // Proceed to the next middleware or route handler
    });
};

module.exports = authMiddleware;
