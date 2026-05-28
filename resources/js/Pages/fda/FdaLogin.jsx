import { useState } from "react";
import axios from "axios";

export default function FdaLogin() {
  const [userName, setUserName] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');

  const handleLogin = async (e) => {
    e.preventDefault();
    setError(''); // Clear previous errors
    
    // 1. Get the CSRF token from the meta tag in your blade file
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    try {
        // Updated path to /fda/login to match your web.php routes
        const res = await axios.post('/fda/login', 
            { userName, password }, 
            {
                headers: {
                    // Critical header to prevent CSRF token mismatch
                    'X-CSRF-TOKEN': csrfToken, 
                    'Accept': 'application/json'
                }
            }
        );
        
        if (res.data.success) {
            // Updated redirect to the protected /fda/dashboard
            window.location.href = '/fda/dashboard';
        }
    } catch (err) {
        // Handle 419 (Token Mismatch), 401 (Auth Failed), or 500 (Server Crash)
        if (err.response?.status === 419) {
          setError('Session expired. Please refresh the page.');
        } else {
          setError(err.response?.data?.message || 'Login failed. Please check your credentials.');
        }
    }
  };

  return (
    <div className="flex items-center justify-center min-h-screen bg-gray-100">
      <form 
        onSubmit={handleLogin} 
        className="bg-white p-8 rounded-2xl shadow-lg w-96 flex flex-col"
      >
        <h2 className="text-xl font-extrabold mb-4 text-green-700 text-center">
          FDA Verification Portal Admin
        </h2>
        
        {error && (
          <p className="text-red-500 mb-4 text-center font-medium bg-red-50 p-2 rounded">
            {error}
          </p>
        )}

        <input 
          type="text" 
          placeholder="Username" 
          value={userName} 
          onChange={(e) => setUserName(e.target.value)}
          required
          className="border border-gray-300 focus:border-green-600 focus:ring-1 focus:ring-green-600 rounded-lg p-3 mb-4 outline-none transition"
        />

        <input 
          type="password" 
          placeholder="Password" 
          value={password} 
          onChange={(e) => setPassword(e.target.value)}
          required
          className="border border-gray-300 focus:border-green-600 focus:ring-1 focus:ring-green-600 rounded-lg p-3 mb-6 outline-none transition"
        />

        <button 
          type="submit" 
          className="bg-[#00bf63] hover:bg-[#286634] text-white font-semibold py-3 rounded-lg transition shadow-md active:scale-95"
        >
          Login
        </button>

        <p className="text-gray-500 text-sm mt-4 text-center">
          &copy; {new Date().getFullYear()} All Rights Reserved.
        </p>
      </form>
    </div>
  );
}