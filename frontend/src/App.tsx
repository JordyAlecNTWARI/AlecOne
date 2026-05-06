import { BrowserRouter, Routes, Route, Link } from 'react-router-dom';
import { AuthProvider, useAuth } from './context/AuthContext';
import HomePage from './pages/HomePage';
import LoginPage from './pages/LoginPage';
import RegisterPage from './pages/RegisterPage';
import CataloguePage from './pages/CataloguePage';

function Navbar() {
    const { isAuthenticated, logout } = useAuth();
    return (
        <nav style={{ padding: '10px', background: '#1a1a2e', color: 'white', display: 'flex', gap: '20px' }}>
            <Link to="/" style={{ color: 'white' }}>Accueil</Link>
            <Link to="/catalogue" style={{ color: 'white' }}>Catalogue</Link>
            {isAuthenticated ? (
                <button onClick={logout} style={{ color: 'white', background: 'none', border: 'none', cursor: 'pointer' }}>Déconnexion</button>
            ) : (
                <>
                    <Link to="/login" style={{ color: 'white' }}>Connexion</Link>
                    <Link to="/register" style={{ color: 'white' }}>Inscription</Link>
                </>
            )}
        </nav>
    );
}

function App() {
    return (
        <AuthProvider>
            <BrowserRouter>
                <Navbar />
                <div style={{ padding: '20px' }}>
                    <Routes>
                        <Route path="/" element={<HomePage />} />
                        <Route path="/catalogue" element={<CataloguePage />} />
                        <Route path="/login" element={<LoginPage />} />
                        <Route path="/register" element={<RegisterPage />} />
                    </Routes>
                </div>
            </BrowserRouter>
        </AuthProvider>
    );
}

export default App;
