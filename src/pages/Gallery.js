import React, { useState, useEffect } from 'react';
import { useTranslation } from 'react-i18next';
import AlbumGrid from '../components/Gallery/AlbumGrid';
import AlbumModal from '../components/Gallery/AlbumModal';

const Gallery = () => {
  const { t, i18n } = useTranslation();
  const [albums, setAlbums] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [selectedAlbum, setSelectedAlbum] = useState(null);
  const [page, setPage] = useState(1);
  const [hasMore, setHasMore] = useState(true);
  const albumsPerPage = 12;

  useEffect(() => {
    fetchAlbums();
  }, [i18n.language, page]);

  const fetchAlbums = async () => {
    try {
      setLoading(true);
      const response = await fetch(`/api/albums.php?lang=${i18n.language}&page=${page}&per_page=${albumsPerPage}`);
      if (!response.ok) {
        throw new Error('Failed to fetch albums');
      }
      const data = await response.json();
      
      if (page === 1) {
        setAlbums(data.albums);
      } else {
        setAlbums(prev => [...prev, ...data.albums]);
      }
      
      setHasMore(data.hasMore);
      setError(null);
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  };

  const handleAlbumClick = (album) => {
    setSelectedAlbum(album);
  };

  const handleCloseModal = () => {
    setSelectedAlbum(null);
  };

  const loadMore = () => {
    if (!loading && hasMore) {
      setPage(prev => prev + 1);
    }
  };

  if (error) {
    return <div className="gallery__error">Error: {error}</div>;
  }

  return (
    <div className="gallery">
      <h1 className="gallery__title">{t('gallery.title')}</h1>
      <AlbumGrid 
        albums={albums}
        onAlbumClick={handleAlbumClick}
        loading={loading}
        hasMore={hasMore}
        onLoadMore={loadMore}
      />

      {selectedAlbum && (
        <AlbumModal
          album={selectedAlbum}
          onClose={handleCloseModal}
          language={i18n.language}
        />
      )}
    </div>
  );
};

export default Gallery; 