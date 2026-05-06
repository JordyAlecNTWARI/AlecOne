import { BrowserRouter, Routes, Route, Link } from 'react-router-dom';
import { AuthProvider, useAuth } from './context/AuthContext';
import HomePage from './pages/HomePage';
import LoginPage from './pages/LoginPage';
import RegisterPage from './pages/RegisterPage';
import CataloguePage from './pages/CataloguePage';
import ResourceDetailPage from './pages/ResourceDetailPage';
import ProfilePage from './pages/ProfilePage';
import DashboardPage from './pages/admin/DashboardPage';
import ResourceFormPage from './pages/admin/ResourceFormPage';

function Navbar() {
    const { isAuthenticated, logout } = useAuth();
    return (
        <nav style={{ padding: '10px', background: '#1a1a2e', color: 'white', display: 'flex', gap: '20px' }}>
            <Link to="/" style={{ color: 'white' }}>Accueil</Link>
            <Link to="/catalogue" style={{ color: 'white' }}>Catalogue</Link>
            {isAuthenticated ? (
                <>
                    <Link to="/profile" style={{ color: 'white' }}>Mon profil</Link>
                    <Link to="/admin" style={{ color: '#7c6dfa' }}>Admin</Link>
                    <button onClick={logout} style={{ color: 'white', background: 'none', border: 'none', cursor: 'pointer' }}>Déconnexion</button>
                </>
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
                        <Route path="/resources/:id" element={<ResourceDetailPage />} />
                        <Route path="/login" element={<LoginPage />} />
                        <Route path="/register" element={<RegisterPage />} />
                        <Route path="/profile" element={<ProfilePage />} />
                        <Route path="/admin" element={<DashboardPage />} />
                        <Route path="/admin/resources/new" element={<ResourceFormPage />} />
                        <Route path="/admin/resources/:id/edit" element={<ResourceFormPage />} />
                    </Routes>
                </div>
            </BrowserRouter>
        </AuthProvider>
    );
}

export default App;
