import { useEffect, useState } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import client from '../../api/client';

interface Playlist {
    id: number;
    name: string;
}

function ResourceFormPage() {
    const { id } = useParams();
    const navigate = useNavigate();
    const isEdit = !!id;

    const [form, setForm] = useState({
        title: '',
        type: 'Formation',
        description: '',
        url: '',
        publishedAt: '',
        playlistId: '',
    });
    const [playlists, setPlaylists] = useState<Playlist[]>([]);
    const [message, setMessage] = useState('');
    const [error, setError] = useState('');

    useEffect(() => {
        client.get('/playlists').then(res => setPlaylists(res.data));

        if (isEdit) {
            client.get(`/resources/${id}`).then(res => {
                const r = res.data;
                setForm({
                    title: r.title,
                    type: r.type,
                    description: r.description ?? '',
                    url: r.url ?? '',
                    publishedAt: r.publishedAt,
                    playlistId: r.playlist?.id ?? '',
                });
            });
        }
    }, [id, isEdit]);

    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>) => {
        setForm({ ...form, [e.target.name]: e.target.value });
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setError('');
        try {
            if (isEdit) {
                await client.put(`/resources/${id}`, form);
                setMessage('Ressource mise à jour avec succès');
            } else {
                await client.post('/resources', form);
                setMessage('Ressource créée avec succès');
            }
            setTimeout(() => navigate('/admin'), 1500);
        } catch {
            setError('Erreur lors de la sauvegarde');
        }
    };

    return (
        <div>
            <h1>{isEdit ? 'Modifier la ressource' : 'Ajouter une ressource'}</h1>
            {message && <p style={{ color: 'green' }}>{message}</p>}
            {error && <p style={{ color: 'red' }}>{error}</p>}
            <form onSubmit={handleSubmit}>
                <div>
                    <label>Titre</label>
                    <input name="title" value={form.title} onChange={handleChange} required />
                </div>
                <div>
                    <label>Type</label>
                    <select name="type" value={form.type} onChange={handleChange}>
                        <option value="Formation">Formation</option>
                        <option value="Livre">Livre</option>
                        <option value="Video">Video</option>
                    </select>
                </div>
                <div>
                    <label>Description</label>
                    <textarea name="description" value={form.description} onChange={handleChange} />
                </div>
                <div>
                    <label>URL</label>
                    <input name="url" value={form.url} onChange={handleChange} />
                </div>
                <div>
                    <label>Date de publication</label>
                    <input type="date" name="publishedAt" value={form.publishedAt} onChange={handleChange} required />
                </div>
                <div>
                    <label>Playlist</label>
                    <select name="playlistId" value={form.playlistId} onChange={handleChange}>
                        <option value="">Aucune playlist</option>
                        {playlists.map(p => (
                            <option key={p.id} value={p.id}>{p.name}</option>
                        ))}
                    </select>
                </div>
                <button type="submit">{isEdit ? 'Mettre à jour' : 'Créer'}</button>
                <button type="button" onClick={() => navigate('/admin')} style={{ marginLeft: '10px' }}>Annuler</button>
            </form>
        </div>
    );
}

export default ResourceFormPage;
