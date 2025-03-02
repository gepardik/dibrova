import i18n from 'i18next';
import { initReactI18next } from 'react-i18next';
import LanguageDetector from 'i18next-browser-languagedetector';

i18n
  .use(LanguageDetector)
  .use(initReactI18next)
  .init({
    resources: {
      en: {
        translation: {
          nav: {
            home: 'Home',
            concerts: 'Concerts',
            gallery: 'Gallery',
            contact: 'Contact'
          },
          common: {
            loading: 'Loading',
            back: 'Back'
          },
          home: {
            title: 'About Us',
            description: '<p>The <strong>DIBROVA</strong> Ukrainian Folk Instrument Ensemble was founded in 2000 in Tallinn by music and singing enthusiast <strong>Lidiya Kirilyuk</strong>.</p><p>In 2010, the ensemble was led by <strong>Natalia Voronova</strong>, a graduate of the <strong>Rostov Conservatory</strong> and a five-row button accordion player, who elevated the group to a higher professional level.</p><p>Since 2023, the ensemble has been directed by <strong>Olena Duminika</strong>, a graduate of the <strong>Odesa Conservatory</strong> specializing in the domra. <strong>Olena</strong> is also the author of all <strong>DIBROVA\'s</strong> arrangements.</p><p>The ensemble\'s repertoire includes folk, classical, and contemporary music. In recent years, many professional musicians have joined <strong>DIBROVA</strong>, allowing the ensemble to perform even the most complex works of world classical music.</p><p>The ensemble is primarily composed of domras, which are the analogs of the violin, viola, and cello. A true highlight of the group is the unique Ukrainian folk instrument bandura, played by <strong>Liudmyla Gramyak</strong>, a graduate of the <strong>Lviv Music Academy</strong>.</p><p><strong>DIBROVA</strong> is a laureate of numerous Estonian festivals and competitions.</p><p>Ensemble members:</p><ul><li><strong>Olena Duminika</strong> (artistic director, domra-prima, bouzouki)</li><li><strong>Natalia Skrypnyk</strong> (domra-prima)</li><li><strong>Iryna Butkova</strong> (domra-tenor)</li><li><strong>Liudmyla Gramyak</strong> (bandura)</li><li><strong>Galina Morozova</strong> (classical guitar)</li></ul>'
          },
          concerts: {
            title: 'Concerts',
            upcoming: 'Upcoming Concerts',
            past: 'Past Concerts',
            noUpcoming: 'No upcoming concerts at the moment',
            noPast: 'No past concerts',
            date: 'Date',
            time: 'Time',
            venue: 'Venue',
            price: 'Price',
            buyTickets: 'Buy Tickets',
            notFound: 'Concert not found',
            fetchError: 'Failed to load concert details',
            free: 'Free'
          },
          contact: {
            title: 'Contact Us',
            description: 'Have questions? We\'d love to hear from you. Send us a message and we\'ll respond as soon as possible.',
            form: {
              name: 'Name',
              email: 'Email',
              message: 'Message',
              send: 'Send Message',
              success: 'Message sent successfully!',
              error: 'An error occurred. Please try again.'
            },
            info: {
              email: 'Email',
              phone: 'Phone',
              address: 'Address'
            }
          },
          gallery: {
            title: "Gallery",
            photos: "Photos",
            videos: "Videos",
            noAlbums: "No albums found",
            loadMore: "Load more"
          }
        }
      },
      ru: {
        translation: {
          nav: {
            home: 'Главная',
            concerts: 'Концерты',
            gallery: 'Галерея',
            contact: 'Контакт'
          },
          common: {
            loading: 'Загрузка',
            back: 'Назад'
          },
          home: {
            title: 'О нас',
            description: '<p>Ансамбль украинских народных инструментов <strong>DIBROVA</strong> был создан в 2000 году в Таллине любителем музыки и пения <strong>Лидией Кирилюк</strong>.</p><p>В 2010 году руководителем ансамбля стала выпускница <strong>Ростовской консерватории</strong>, исполнительница на пятирядной гармони <strong>Наталия Воронова</strong>, которая вывела коллектив на более высокий профессиональный уровень.</p><p>С 2023 года ансамблем руководит <strong>Олена Думиника</strong>, окончившая <strong>Одесскую консерваторию</strong> по классу домры. <strong>Олена</strong> также является автором всех аранжировок ансамбля.</p><p>Репертуар <strong>DIBROVA</strong> включает произведения народной, классической и современной музыки. В последние годы к ансамблю присоединилось множество профессиональных музыкантов, благодаря чему коллектив исполняет даже самые сложные произведения мировой классики.</p><p>Основу состава ансамбля составляют домры, являющиеся аналогами скрипки, альта и виолончели. Настоящим украшением коллектива является уникальный украинский народный инструмент бандура, на котором играет выпускница <strong>Львовской музыкальной академии</strong> <strong>Людмила Грамяк</strong>.</p><p><strong>DIBROVA</strong> является лауреатом множества эстонских фестивалей и конкурсов.</p><p>Участники ансамбля:</p><ul><li><strong>Олена Думиника</strong> (художественный руководитель, домра-прима, бузуки)</li><li><strong>Наталия Скирпник</strong> (домра-прима)</li><li><strong>Ирина Буткова</strong> (домра-тенор)</li><li><strong>Людмила Грамяк</strong> (бандура)</li><li><strong>Галина Морозова</strong> (классическая гитара)</li></ul>'
          },
          concerts: {
            title: 'Концерты',
            upcoming: 'Предстоящие концерты',
            past: 'Прошедшие концерты',
            noUpcoming: 'На данный момент нет предстоящих концертов',
            noPast: 'Нет прошедших концертов',
            date: 'Дата',
            time: 'Время',
            venue: 'Место проведения',
            price: 'Цена',
            buyTickets: 'Купить билеты',
            notFound: 'Концерт не найден',
            fetchError: 'Ошибка загрузки информации о концерте',
            free: 'Бесплатно'
          },
          contact: {
            title: 'Свяжитесь с нами',
            description: 'Есть вопросы? Мы будем рады вам помочь. Отправьте нам сообщение, и мы ответим как можно скорее.',
            form: {
              name: 'Имя',
              email: 'Email',
              message: 'Сообщение',
              send: 'Отправить',
              success: 'Сообщение успешно отправлено!',
              error: 'Произошла ошибка. Пожалуйста, попробуйте еще раз.'
            },
            info: {
              email: 'Email',
              phone: 'Телефон',
              address: 'Адрес'
            }
          },
          gallery: {
            title: "Галерея",
            photos: "Фотографии",
            videos: "Видео",
            noAlbums: "Альбомов не найдено",
            loadMore: "Загрузить ещё"
          }
        }
      },
      et: {
        translation: {
          nav: {
            home: 'Avaleht',
            concerts: 'Kontserdid',
            gallery: 'Galerii',
            contact: 'Kontakt'
          },
          common: {
            loading: 'Laadimine',
            back: 'Tagasi'
          },
          home: {
            title: 'Meist',
            description: '<p><strong>DIBROVA</strong> Ukraina rahvapillide ansambel loodi 2000. aastal Tallinnas muusika ja laulmise entusiasti <strong>Lidiya Kirilyuki</strong> poolt.</p><p>2010. aastal sai ansambli juhiks <strong>Natalia Voronova</strong>, kes on lõpetanud <strong>Rostovi Konservatooriumi</strong> ja mängib viierealist nuppakordionit. Tema juhtimisel saavutas ansambel kõrgema professionaalse taseme.</p><p>Alates 2023. aastast juhib ansamblit <strong>Olena Duminika</strong>, kes on lõpetanud <strong>Odessa Konservatooriumi</strong> domra erialal. <strong>Olena</strong> on ka kõigi <strong>DIBROVA</strong> seadetööde autor.</p><p>Ansambli repertuaar hõlmab rahvamuusikat, klassikalist ja kaasaegset muusikat. Viimastel aastatel on <strong>DIBROVA</strong>-ga liitunud mitmeid professionaalseid muusikuid, tänu kellele suudab ansambel esitada ka maailma klassika kõige keerukamaid teoseid.</p><p>Ansambli põhikoosseisu moodustavad domrad, mis on viiulite, vioolade ja tšellode analoogid. Ansambli tõeliseks pärliks on ainulaadne Ukraina rahvapill bandura, mida mängib <strong>Liudmyla Gramyak</strong>, kes on lõpetanud <strong>Lvivi Muusikaakadeemia</strong>.</p><p><strong>DIBROVA</strong> on mitmete Eesti festivalide ja konkursside laureaat.</p><p>Ansambli liikmed:</p><ul><li><strong>Olena Duminika</strong> (kunstiline juht, domra-prima, buzuk)</li><li><strong>Natalia Skrypnyk</strong> (domra-prima)</li><li><strong>Iryna Butkova</strong> (domra-tenor)</li><li><strong>Liudmyla Gramyak</strong> (bandura)</li><li><strong>Galina Morozova</strong> (klassikaline kitarr)</li></ul>'
          },
          concerts: {
            title: 'Kontserdid',
            upcoming: 'Tulevased kontserdid',
            past: 'Möödunud kontserdid',
            noUpcoming: 'Hetkel tulevasi kontserte pole',
            noPast: 'Möödunud kontserte pole',
            date: 'Kuupäev',
            time: 'Aeg',
            venue: 'Toimumiskoht',
            price: 'Hind',
            buyTickets: 'Osta piletid',
            notFound: 'Kontserti ei leitud',
            fetchError: 'Kontserdi andmete laadimine ebaõnnestus',
            free: 'Tasuta'
          },
          contact: {
            title: 'Võta meiega ühendust',
            description: 'Kas teil on küsimusi? Meil on hea meel teiega suhelda. Saatke meile sõnum ja vastame teile esimesel võimalusel.',
            form: {
              name: 'Nimi',
              email: 'Email',
              message: 'Sõnum',
              send: 'Saada',
              success: 'Sõnum on edukalt saadetud!',
              error: 'Tekkis viga. Palun proovige uuesti.'
            },
            info: {
              email: 'Email',
              phone: 'Telefon',
              address: 'Aadress'
            }
          },
          gallery: {
            title: "Galerii",
            photos: "Pildid",
            videos: "Videos",
            noAlbums: "No albums found",
            loadMore: "Load more"
          }
        }
      },
      uk: {
        translation: {
          nav: {
            home: 'Головна',
            concerts: 'Концерти',
            gallery: 'Галерея',
            contact: 'Контакт'
          },
          common: {
            loading: 'Завантаження',
            back: 'Назад'
          },
          home: {
            title: 'Про нас',
            description: '<p>Ансамбль українських народних інструментів <strong>DIBROVA</strong> був створений у 2000 році в Таллінні любителем музики та співу <strong>Лідією Кирилюк</strong>.</p><p>У 2010 році керівником ансамблю стала випускниця <strong>Ростовської консерваторії</strong>, виконавиця на п\'ятирядній гармоні <strong>Наталія Воронова</strong>, яка вивела колектив на вищий професійний рівень.</p><p>З 2023 року ансамблем керує <strong>Олена Думініка</strong>, яка закінчила <strong>Одеську консерваторію</strong> за спеціальністю «домра». <strong>Олена</strong> також є авторкою всіх аранжувань ансамблю.</p><p>Репертуар <strong>DIBROVA</strong> включає твори народної, класичної та сучасної музики. В останні роки до ансамблю приєдналося багато професійних музикантів, завдяки чому колектив виконує навіть найскладніші твори світової класики.</p><p>Основу складу ансамблю становлять домри, які є аналогами скрипки, альта та віолончелі. Справжньою окрасою колективу є унікальний український народний інструмент бандура, на якій грає випускниця <strong>Львівської музичної академії</strong> <strong>Людмила Грам\'як</strong>.</p><p><strong>DIBROVA</strong> є лауреатом багатьох естонських фестивалів і конкурсів.</p><p>Учасники ансамблю:</p><ul><li><strong>Олена Думініка</strong> (художній керівник, домра-прима, бузукі)</li><li><strong>Наталія Скрипник</strong> (домра-прима)</li><li><strong>Ірина Буткова</strong> (домра-тенор)</li><li><strong>Людмила Грам\'як</strong> (бандура)</li><li><strong>Галина Морозова</strong> (класична гітара)</li></ul>'
          },
          concerts: {
            title: 'Концерти',
            upcoming: 'Майбутні концерти',
            past: 'Минулі концерти',
            noUpcoming: 'На даний момент немає майбутніх концертів',
            noPast: 'Немає минулих концертів',
            date: 'Дата',
            time: 'Час',
            venue: 'Місце проведення',
            price: 'Ціна',
            buyTickets: 'Купити квитки',
            notFound: 'Концерт не знайдено',
            fetchError: 'Помилка завантаження інформації про концерт',
            free: 'Безкоштовно'
          },
          contact: {
            title: 'Зв\'яжіться з нами',
            description: 'Є питання? Ми будемо раді вам допомогти. Надішліть нам повідомлення, і ми відповімо якнайшвидше.',
            form: {
              name: 'Ім\'я',
              email: 'Email',
              message: 'Повідомлення',
              send: 'Надіслати',
              success: 'Повідомлення успішно надіслано!',
              error: 'Сталася помилка. Будь ласка, спробуйте ще раз.'
            },
            info: {
              email: 'Email',
              phone: 'Телефон',
              address: 'Адреса'
            }
          },
          gallery: {
            title: "Галерея",
            photos: "Фотографії",
            videos: "Відео",
            noAlbums: "Альбомів не знайдено",
            loadMore: "Завантажити ще"
          }
        }
      }
    },
    fallbackLng: 'ru',
    interpolation: {
      escapeValue: false
    }
  });

export default i18n; 