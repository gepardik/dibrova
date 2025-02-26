import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import { useTranslation } from 'react-i18next';

const Navbar = () => {
  const { t, i18n } = useTranslation();
  const [isMenuOpen, setIsMenuOpen] = useState(false);
  const [isLangMenuOpen, setIsLangMenuOpen] = useState(false);

  const changeLanguage = (lng) => {
    i18n.changeLanguage(lng);
    setIsLangMenuOpen(false);
  };

  const toggleMenu = () => {
    setIsMenuOpen(!isMenuOpen);
  };

  const toggleLangMenu = () => {
    setIsLangMenuOpen(!isLangMenuOpen);
  };

  // Функция для получения короткого кода языка
  const getShortLangCode = (langCode) => {
    return langCode.split('-')[0].toUpperCase();
  };

  return (
    <nav className="navbar">
      <div className="navbar__container">
        <button className="navbar__mobile-toggle" onClick={toggleMenu}>
          <i className={`fas ${isMenuOpen ? 'fa-times' : 'fa-bars'}`}></i>
        </button>

        <div className="navbar__logo">
          <Link to="/" onClick={() => setIsMenuOpen(false)}>
            <img src="/images/logo.svg" alt="DIBROVA" />
          </Link>
        </div>
        
        <div className={`navbar__menu ${isMenuOpen ? 'active' : ''}`}>
          <Link to="/" className="navbar__link" onClick={() => setIsMenuOpen(false)} translate="no">{t('nav.home')}</Link>
          <Link to="/concerts" className="navbar__link" onClick={() => setIsMenuOpen(false)} translate="no">{t('nav.concerts')}</Link>
          <Link to="/contact" className="navbar__link" onClick={() => setIsMenuOpen(false)} translate="no">{t('nav.contact')}</Link>
        </div>

        <div className={`navbar__lang ${isMenuOpen ? 'active' : ''}`} translate="no">
          <button className="navbar__lang-toggle" onClick={toggleLangMenu}>
            {getShortLangCode(i18n.language)}
            <i className={`fas fa-chevron-${isLangMenuOpen ? 'up' : 'down'}`}></i>
          </button>
          <div className={`navbar__lang-dropdown ${isLangMenuOpen ? 'active' : ''}`}>
            <button onClick={() => changeLanguage('et')} translate="no">ET</button>
            <button onClick={() => changeLanguage('uk')} translate="no">UK</button>
            <button onClick={() => changeLanguage('ru')} translate="no">RU</button>
            <button onClick={() => changeLanguage('en')} translate="no">EN</button>
          </div>
        </div>

        <div className={`navbar__social ${isMenuOpen ? 'active' : ''}`}>
          <a href="https://www.facebook.com/profile.php?id=100095216659393" 
             target="_blank" 
             rel="noopener noreferrer"
             title="Facebook">
            <i className="fab fa-facebook-f"></i>
          </a>
          <a href="#" 
             target="_blank" 
             rel="noopener noreferrer"
             title="Instagram">
            <i className="fab fa-instagram"></i>
          </a>
        </div>
      </div>
    </nav>
  );
};

export default Navbar; 