import React, { useRef, useEffect } from 'react';
import { useTranslation } from 'react-i18next';
import AlbumCard from './AlbumCard';

const AlbumGrid = ({ albums, onAlbumClick, loading, hasMore, onLoadMore }) => {
  const { t } = useTranslation();
  const observerRef = useRef();
  const loadingRef = useRef();

  useEffect(() => {
    const options = {
      root: null,
      rootMargin: '20px',
      threshold: 1.0
    };

    observerRef.current = new IntersectionObserver((entries) => {
      const [entry] = entries;
      if (entry.isIntersecting && hasMore && !loading) {
        onLoadMore();
      }
    }, options);

    if (loadingRef.current) {
      observerRef.current.observe(loadingRef.current);
    }

    return () => {
      if (observerRef.current) {
        observerRef.current.disconnect();
      }
    };
  }, [hasMore, loading, onLoadMore]);

  if (!albums.length && !loading) {
    return (
      <div className="gallery__empty">
        {t('gallery.noAlbums')}
      </div>
    );
  }

  return (
    <div className="gallery__grid">
      {albums.map(album => (
        <AlbumCard
          key={album.id}
          album={album}
          onClick={() => onAlbumClick(album)}
        />
      ))}
      
      {(loading || hasMore) && (
        <div ref={loadingRef} className="gallery__loading">
          {loading ? t('common.loading') : ''}
        </div>
      )}
    </div>
  );
};

export default AlbumGrid; 