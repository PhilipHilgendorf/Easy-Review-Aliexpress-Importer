import React, { useState } from 'react';

function EraiSelect({ name, options, id, value, onChange }) {

    return (
        <select 
            className="erai-controll" 
            name={name} 
            id={id} 
            value={value} 
            onChange={onChange}
        >
            {options.map((option, index) => (
                <option key={index} value={option.value}>
                    {option.label}
                </option>
            ))}
        </select>
    );
}

export default EraiSelect;
