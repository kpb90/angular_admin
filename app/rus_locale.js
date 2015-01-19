appConfig.constant('rus_locale', 
	{
      "DATETIME_FORMATS": {
        "AMPMS": [
          "AM",
          "PM"
        ],
        "DAY": [
          "Воскресенье",
          "Понедельник",
          "Вторник",
          "Среда",
          "Четверг",
          "Пятница",
          "Суббота"
        ],
        "MONTH": [
          "Январь",
          "Февраль",
          "Март",
          "Апрель",
          "Май",
          "Июнь",
          "Июль",
          "Август",
          "Сентябрь",
          "Октябрь",
          "Ноябрь",
          "Декабрь"
        ],
        "SHORTDAY": [
          "Вс",
          "Пн",
          "Вт",
          "Ср",
          "Чт",
          "Пт",
          "Сб"
        ],
        "SHORTMONTH": [
          "Янв",
          "Фер",
          "Мар",
          "Апр",
          "Май",
          "Июн",
          "Июл",
          "Авг",
          "Сеп",
          "Окт",
          "Ноя",
          "Дек"
        ],
        "fullDate": "EEEE, MMMM d, y",
        "longDate": "MMMM d, y",
        "medium": "MMM d, y h:mm:ss a",
        "mediumDate": "MMM d, y",
        "mediumTime": "h:mm:ss a",
        "short": "M/d/yy h:mm a",
        "shortDate": "M/d/yy",
        "shortTime": "h:mm a"
      },
      "NUMBER_FORMATS": {
        "CURRENCY_SYM": "$",
        "DECIMAL_SEP": ".",
        "GROUP_SEP": ",",
        "PATTERNS": [
          {
            "gSize": 3,
            "lgSize": 3,
            "macFrac": 0,
            "maxFrac": 3,
            "minFrac": 0,
            "minInt": 1,
            "negPre": "-",
            "negSuf": "",
            "posPre": "",
            "posSuf": ""
          },
          {
            "gSize": 3,
            "lgSize": 3,
            "macFrac": 0,
            "maxFrac": 2,
            "minFrac": 2,
            "minInt": 1,
            "negPre": "(\u00a4",
            "negSuf": ")",
            "posPre": "\u00a4",
            "posSuf": ""
          }
        ]
      },
      "id": "en-us",
      "pluralCat": function (n) {
        if (n == 1) {
          return PLURAL_CATEGORY.ONE;
        }
        return PLURAL_CATEGORY.OTHER;
      }
    }
);