import React, { useState, useEffect, useRef } from 'react';
import { useTranslation } from 'react-i18next';
import PhotoSwipe from 'photoswipe';
import PhotoSwipeLightbox from 'photoswipe/lightbox';
import 'photoswipe/style.css';

const AlbumModal = ({ album, onClose }) => {
  const { t } = useTranslation();
  const [albumData, setAlbumData] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [photosDimensions, setPhotosDimensions] = useState({});
  const [selectedVideo, setSelectedVideo] = useState(null);
  const lightboxRef = useRef(null);
  const galleryRef = useRef(null);

  useEffect(() => {
    if (album?.id) {
      fetchAlbumContent();
    }
  }, [album?.id]);

  useEffect(() => {
    if (!albumData?.photos) return;

    // Загружаем размеры изображений
    albumData.photos.forEach(photo => {
      const img = new Image();
      img.onload = () => {
        setPhotosDimensions(prev => ({
          ...prev,
          [photo.id]: {
            width: img.naturalWidth,
            height: img.naturalHeight
          }
        }));
      };
      img.src = `/${photo.original_path}`;
    });
  }, [albumData?.photos]);

  useEffect(() => {
    if (!albumData || !galleryRef.current) return;

    // Initialize PhotoSwipe Lightbox
    lightboxRef.current = new PhotoSwipeLightbox({
      gallery: galleryRef.current,
      children: 'a',
      pswpModule: PhotoSwipe,
      
      // Basic options
      showHideAnimationType: 'fade',
      showAnimationDuration: 300,
      hideAnimationDuration: 300,
      
      // UI Elements
      arrowKeys: true,
      imageClickAction: 'zoom',
      tapAction: 'toggle-controls',
      
      // Show UI controls
      showPrevNextButtons: true,
      showZoomButton: true,
      showCounter: true,
      
      // Background opacity
      bgOpacity: 0.9,
      
      // Padding
      padding: { top: 20, bottom: 20, left: 20, right: 20 },
      
      // Error message
      errorMsg: 'Изображение не может быть загружено'
    });

    // Prevent default link behavior
    const links = galleryRef.current.querySelectorAll('a');
    links.forEach(link => {
      link.addEventListener('click', (e) => {
        e.preventDefault();
      });
    });

    lightboxRef.current.init();

    return () => {
      if (lightboxRef.current) {
        lightboxRef.current.destroy();
        lightboxRef.current = null;
      }
    };
  }, [albumData]);

  const fetchAlbumContent = async () => {
    try {
      setLoading(true);
      const response = await fetch(`/api/album.php?id=${album.id}`);
      if (!response.ok) throw new Error('Failed to fetch album content');
      const data = await response.json();
      setAlbumData(data);
      setError(null);
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  };

  const handleVideoClick = (video) => {
    setSelectedVideo(video);
  };

  const closeVideoModal = () => {
    setSelectedVideo(null);
  };

  if (!album) return null;

  return (
    <div className="album-modal">
      <button className="album-modal__close" onClick={onClose}>×</button>
      
      {loading && <div className="album-modal__loading">{t('common.loading')}</div>}
      {error && <div className="album-modal__error">{error}</div>}
      
      {albumData && (
        <div className="album-modal__content">
          <h2 className="album-modal__title">{albumData.title}</h2>
          
          <div ref={galleryRef} className="album-modal__gallery">
            {albumData.photos?.map((photo) => {
              const dimensions = photosDimensions[photo.id] || {};
              return (
                <a
                  href={`/${photo.original_path}`}
                  data-pswp-width={dimensions.width}
                  data-pswp-height={dimensions.height}
                  key={photo.id}
                  onClick={(e) => e.preventDefault()}
                  className={!dimensions.width ? 'loading' : ''}
                >
                  <img 
                    src={`/${photo.thumbnail_path}`} 
                    alt="" 
                    style={{
                      aspectRatio: dimensions.width ? `${dimensions.width}/${dimensions.height}` : 'auto'
                    }}
                  />
                </a>
              );
            })}
          </div>
          
          {albumData.videos?.length > 0 && (
            <div className="album-modal__videos">
              {albumData.videos.map(video => (
                <div 
                  className="album-modal__video" 
                  key={video.id}
                  onClick={() => handleVideoClick(video)}
                >
                  <div className="album-modal__video-thumbnail">
                    <img 
                      src={`https://img.youtube.com/vi/${video.youtube_id}/maxresdefault.jpg`}
                      alt={video.title}
                    />
                    <div className="album-modal__video-play">▶</div>
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>
      )}

      {selectedVideo && (
        <div className="video-modal">
          <div className="video-modal__content">
            <button className="video-modal__close" onClick={closeVideoModal}>×</button>
            <iframe
              width="1280"
              height="720"
              src={`https://www.youtube.com/embed/${selectedVideo.youtube_id}?autoplay=1`}
              title={selectedVideo.title}
              frameBorder="0"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
              allowFullScreen
            ></iframe>
          </div>
        </div>
      )}
    </div>
  );
};

export default AlbumModal; 