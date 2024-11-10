import React, { useState, useEffect } from 'react';

function EraiSaveLoader({ status }) {
    const [statusContent, setStatusContent] = useState('');
   
    const [display, setDisplay] = useState('none');
    const [opacity, setOpacity] = useState(1);

    useEffect(() => {
        if (status === 'loading') {
            setDisplay('block');
            setOpacity(1);
            setStatusContent('');
        } else if (status === 'success') {
            setDisplay('block');
            setOpacity(1);
            setStatusContent('Saved successfully!');
            // Hide after 3 seconds
            const timer = setTimeout(() => setOpacity(0), 2000);
            return () => clearTimeout(timer); // Clear timer on cleanup
        } else if (status === 'error') {
            setDisplay('block');
            setOpacity(1);
            setStatusContent('Save failed.');
            // Hide after 3 seconds
            const timer = setTimeout(() => setOpacity(0), 2000);
            return () => clearTimeout(timer); // Clear timer on cleanup
        } else {
            setDisplay('none');
            setStatusContent('');
        }
    }, [status]);

    useEffect(() => {
        // After fade-out, set display to 'none' to hide completely
        if (opacity === 0) {
            const hideDisplayTimer = setTimeout(() => setDisplay('none'), 500); // Additional delay for smooth transition
            return () => clearTimeout(hideDisplayTimer);
        }
    }, [opacity]);

    const renderIcon = () => {
        switch (status) {
            case 'loading':
                return (
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className="loader">
                        <polyline points="23 4 23 10 17 10"></polyline>
                        <polyline points="1 20 1 14 7 14"></polyline>
                        <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                    </svg>
                );
            case 'success':
                return (
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" class="feather feather-check-circle"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                );
            case 'error':
                return (
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" class="feather feather-x-circle"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                );
            default:
                return null;
        }
    };

    return (
        <div className="saveloader-container" style={{display: display, opacity: opacity, transition: 'opacity 0.5s ease'}}>
            <div className="saveloader">
                {renderIcon()}
                {statusContent != ""? (
                    <span>{statusContent}</span>
                ):null}
            </div>
        </div>
    );
}

export default EraiSaveLoader;
