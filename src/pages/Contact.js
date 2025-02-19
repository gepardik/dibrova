import React, { useState } from 'react';
import { useTranslation } from 'react-i18next';

const Contact = () => {
  const { t } = useTranslation();
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    message: ''
  });
  const [status, setStatus] = useState({
    submitting: false,
    success: false,
    error: null
  });

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value
    }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setStatus({ submitting: true, success: false, error: null });

    try {
      const response = await fetch('/api/contact.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)
      });

      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.error || t('contact.form.error'));
      }

      setStatus({
        submitting: false,
        success: true,
        error: null
      });

      // Очищаем форму после успешной отправки
      setFormData({
        name: '',
        email: '',
        message: ''
      });
    } catch (err) {
      setStatus({
        submitting: false,
        success: false,
        error: err.message
      });
    }
  };

  return (
    <div className="contact">
      <div className="contact__container">
        <div className="contact__info">
          <h1 className="contact__title">{t('contact.title')}</h1>
          <p className="contact__description">{t('contact.description')}</p>
          
          <div className="contact__details">
            <div className="contact__detail">
              <h3>{t('contact.info.email')}</h3>
              <p>dibrova@dibrova.ee</p>
            </div>
            <div className="contact__detail">
              <h3>{t('contact.info.phone')}</h3>
              <p>+372 5674 9715</p>
            </div>
            <div className="contact__detail">
              <h3>{t('contact.info.address')}</h3>
              <p>Tallinn, Estonia</p>
            </div>
          </div>
        </div>

        <form className="contact__form" onSubmit={handleSubmit}>
          {status.error && (
            <div className="contact__error">
              {status.error}
            </div>
          )}
          {status.success && (
            <div className="contact__success">
              {t('contact.form.success')}
            </div>
          )}

          <div className="contact__form-group">
            <label htmlFor="name">{t('contact.form.name')}</label>
            <input
              type="text"
              id="name"
              name="name"
              value={formData.name}
              onChange={handleChange}
              required
              disabled={status.submitting}
            />
          </div>

          <div className="contact__form-group">
            <label htmlFor="email">{t('contact.form.email')}</label>
            <input
              type="email"
              id="email"
              name="email"
              value={formData.email}
              onChange={handleChange}
              required
              disabled={status.submitting}
            />
          </div>

          <div className="contact__form-group">
            <label htmlFor="message">{t('contact.form.message')}</label>
            <textarea
              id="message"
              name="message"
              value={formData.message}
              onChange={handleChange}
              required
              rows="5"
              disabled={status.submitting}
            />
          </div>

          <button 
            type="submit" 
            className="contact__submit"
            disabled={status.submitting}
          >
            {status.submitting ? 'Sending...' : t('contact.form.send')}
          </button>
        </form>
      </div>
    </div>
  );
};

export default Contact; 