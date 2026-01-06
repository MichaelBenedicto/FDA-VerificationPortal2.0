import React from 'react';
import "../css/app.css";
import { createRoot } from 'react-dom/client';
import SearchPage from './Pages/SearchPage';

const el = document.getElementById('root');
const root = createRoot(el);
root.render(<SearchPage />);
