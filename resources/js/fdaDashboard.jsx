import React from 'react';
import { createRoot } from 'react-dom/client';
import FdaDashboard from './Pages/fda/FdaDashboard';
import "../css/app.css";

const el = document.getElementById('root');
const root = createRoot(el);
root.render(<FdaDashboard />);
