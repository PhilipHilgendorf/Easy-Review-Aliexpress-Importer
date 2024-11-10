import React, { useState, useEffect } from 'react';

function EraiSwitch({ name, defaultChecked, onChange }) {
    const [isChecked, setIsChecked] = useState(defaultChecked);
    // Update isChecked if defaultChecked changes
    useEffect(() => {
        setIsChecked(defaultChecked);
    }, [defaultChecked]);

    // Handle checkbox change
    const handleCheckboxChange = (event) => {
        setIsChecked(event.target.checked); // Update internal state
        if (onChange) {
            onChange(event); // Trigger any parent-provided onChange function
        }
    };

    return (
        <label className="erai-switch">
            <input
                className="erai-switch-checkbox"
                name={name}
                type="checkbox"
                checked={isChecked} // Controlled by internal state
                onChange={handleCheckboxChange} // Updates state on click
            />
            <span className="erai-slider round"></span>
        </label>
    );
}

export default EraiSwitch;
