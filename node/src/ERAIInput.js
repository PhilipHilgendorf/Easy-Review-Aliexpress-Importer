import React, { useState } from 'react';

function EraiInput({ name, type, value, onChange }) {

    return (
        <input class="erai-controll" onChange={onChange} name={name} type={type} value={value} ></input>
    );
}

export default EraiInput;
