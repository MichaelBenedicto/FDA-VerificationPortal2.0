import React from 'react';
import { createRoot } from 'react-dom/client';
import FdaLogin from './Pages/fda/FdaLogin';
import "../css/app.css";


const el = document.getElementById('root');
const root = createRoot(el);
root.render(<FdaLogin />);


