// frontend/src/components/SplashScreen.js
import React, { useEffect, useState } from 'react';
import './SplashScreen.css'; // Import the CSS specifically for the splash screen

const SplashScreen = () => {
    const [fadeOut, setFadeOut] = useState(false);

    useEffect(() => {
        const timer = setTimeout(() => {
            setFadeOut(true);
        }, 2000); // Duration for splash screen visibility

        return () => clearTimeout(timer);
    }, []);

    return (
        <div className={`splash-screen ${fadeOut ? 'fade-out' : ''}`}>
            <h1>Multichannel Inventory System</h1>
        </div>
    );
};

export default SplashScreen;
