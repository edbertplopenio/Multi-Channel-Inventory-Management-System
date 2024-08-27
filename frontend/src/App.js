// frontend/src/App.js
import React, { useState, useEffect } from 'react';
import SplashScreen from './components/SplashScreen';
import MainContent from './components/MainContent'; // Your main content component

const App = () => {
    const [showSplash, setShowSplash] = useState(true);

    useEffect(() => {
        const timer = setTimeout(() => {
            setShowSplash(false);
        }, 3000); // Total time for splash screen including fade-out

        return () => clearTimeout(timer);
    }, []);

    return (
        <>
            {showSplash ? <SplashScreen /> : <MainContent />}
        </>
    );
};

export default App;
