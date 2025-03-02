import React from 'react';
import { useTranslation } from 'react-i18next';

const AlbumCard = ({ album, onClick }) => {
  const { t } = useTranslation();
  const photosCount = album.photos_count || 0;
  const videosCount = album.videos_count || 0;
  
  return (
    <div 
      className="album-card"
      onClick={onClick}
      role="button"
      tabIndex={0}
      onKeyPress={(e) => {
        if (e.key === 'Enter') {
          onClick();
        }
      }}
    >
      <div className="album-card__image-container">
        <img 
          src={album.cover_path ? `/${album.cover_path}` : '/images/album-placeholder.jpg'} 
          alt={album.title}
          className="album-card__image"
        />
      </div>
      <div className="album-card__content">
        <h3 className="album-card__title">{album.title}</h3>
        <div className="album-card__info">
          <span className="album-card__count">
            {t('gallery.photos')}: {photosCount}
            {videosCount > 0 && ` • ${t('gallery.videos')}: ${videosCount}`}
          </span>
        </div>
      </div>
    </div>
  );
};

export default AlbumCard; 