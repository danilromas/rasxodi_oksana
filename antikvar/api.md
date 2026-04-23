{
	"info": {
		"version": "1.0.0",
		"title": "API для продавцов",
		"description": "С помощью API для продавцов Вы сможете управлять Вашими лотами на Мешке.\n\nВзаимодействие с сервером происходит по протоколу https, путем отправки POST запросов на API URL в кодировке UTF-8.\n\nСервер принимает запросы либо в формате `application/json`, либо в формате `application/x-www-form-urlencoded`.\nСоответствующий заголовок запроса должен быть указан в запросе.\nОтветы всегда возвращаются в формате `application/json`, кодировка UTF-8.\n\nВсе даты и время указаны в формате `YYYY-MM-DD HH:MM:SS`.\nМетоды, работающие с датами и временем, принимают или возвращают часовой пояс в отдельном поле.\n\nЗа каждый успешный запрос к API списываются очки с API-счёта. Количество очков зависит от метода\nи указано в описании метода."
	},
	"security": [
		{
			"api_key": []
		}
	],
	"openapi": "3.1.0",
	"tags": [
		{
			"name": "Аккаунт",
			"description": "Информация о текущем аккаунте"
		},
		{
			"name": "Справочники",
			"description": "Информация о категориях, валютах, тегах и т.д."
		},
		{
			"name": "Лоты",
			"description": "Методы работы с лотами: создание, обновление, удаление, получение информации о лоте и т.д."
		}
	],
	"servers": [
		{
			"url": "https://meshok.net/sAPIv2"
		}
	],
	"components": {
		"securitySchemes": {
			"api_key": {
				"type": "http",
				"scheme": "bearer"
			}
		},
		"schemas": {
			"YesOrNo": {
				"type": "string",
				"enum": [
					"Y",
					"N"
				],
				"description": "Статус проверенного продавца.\n- Y - Да;\n- N - Нет."
			},
			"GetAccountInfoResponse": {
				"type": "object",
				"properties": {
					"account": {
						"type": "integer",
						"description": "Идентификатор пользователя.",
						"example": 3325567
					},
					"cost": {
						"type": "number",
						"description": "Стоимость выполнения запроса.",
						"example": 1
					},
					"balance": {
						"type": "number",
						"description": "Баланс после выполнения запроса.",
						"example": 29875
					},
					"expire": {
						"type": "number",
						"description": "Время в секундах до истечения действия ключа.",
						"example": 882677
					},
					"success": {
						"type": "integer",
						"description": "Статус выполнения запроса:\n- `1` - успешное выполнение запроса\n- отрицательные значения - ошибка",
						"example": 1
					},
					"result": {
						"type": "object",
						"nullable": true,
						"properties": {
							"checkedSeller": {
								"$ref": "#/components/schemas/YesOrNo"
							},
							"cityId": {
								"type": "number",
								"description": "ID города регистрации."
							},
							"countryId": {
								"type": "number",
								"description": "ID страны регистрации."
							}
						},
						"required": [
							"checkedSeller",
							"cityId",
							"countryId"
						],
						"example": {
							"checkedSeller": "Y",
							"cityId": 32,
							"countryId": 1
						}
					},
					"error": {
						"type": "string",
						"nullable": true,
						"description": "Текст ошибки."
					},
					"errorDetails": {
						"type": "object",
						"nullable": true,
						"additionalProperties": {
							"nullable": true
						},
						"description": "Дополнительные данные ошибки."
					}
				},
				"required": [
					"account",
					"cost",
					"balance",
					"expire",
					"success"
				]
			},
			"CategoryParamOptions": {
				"type": "object",
				"additionalProperties": {
					"type": "number"
				},
				"description": "Список параметров с их идентификаторами."
			},
			"CategoryParamOption": {
				"type": "object",
				"properties": {
					"type": {
						"type": "string",
						"enum": [
							"options"
						],
						"description": "Тип параметра."
					},
					"multi": {
						"type": "boolean",
						"description": "В случае, если признак multi установлен в значение true,\nто у данного параметра допускается использование нескольких опций для одного лота."
					},
					"options": {
						"$ref": "#/components/schemas/CategoryParamOptions"
					}
				},
				"required": [
					"type",
					"multi",
					"options"
				]
			},
			"CategoryParamCheckbox": {
				"type": "object",
				"properties": {
					"type": {
						"type": "string",
						"enum": [
							"checkbox"
						],
						"description": "Тип параметра."
					},
					"id": {
						"type": "integer",
						"description": "Идентификатор параметра"
					}
				},
				"required": [
					"type",
					"id"
				]
			},
			"CategoryParams": {
				"type": "object",
				"additionalProperties": {
					"anyOf": [
						{
							"$ref": "#/components/schemas/CategoryParamOption"
						},
						{
							"$ref": "#/components/schemas/CategoryParamCheckbox"
						}
					]
				},
				"description": "Список параметров раздела, предназначенных для использования в поле categoryParams методов listItem и updateItem."
			},
			"GetCategoryInfoResponse": {
				"type": "object",
				"properties": {
					"account": {
						"type": "integer",
						"description": "Идентификатор пользователя.",
						"example": 3325567
					},
					"cost": {
						"type": "number",
						"description": "Стоимость выполнения запроса.",
						"example": 1
					},
					"balance": {
						"type": "number",
						"description": "Баланс после выполнения запроса.",
						"example": 29875
					},
					"expire": {
						"type": "number",
						"description": "Время в секундах до истечения действия ключа.",
						"example": 882677
					},
					"success": {
						"type": "integer",
						"description": "Статус выполнения запроса:\n- `1` - успешное выполнение запроса\n- отрицательные значения - ошибка",
						"example": 1
					},
					"result": {
						"type": "object",
						"nullable": true,
						"properties": {
							"id": {
								"type": "integer",
								"description": "Идентификатор тематического раздела."
							},
							"name": {
								"type": "string",
								"description": "Название тематического раздела."
							},
							"parentId": {
								"type": "integer",
								"description": "Идентификатор родительского раздела."
							},
							"subCategories": {
								"type": "boolean",
								"description": "Наличие подразделов, принимает значения:\ntrue - подразделы есть\nfalse - подразделов нет."
							},
							"recommendedPrice": {
								"type": "number",
								"description": "Только для разделов без подразделов: Стоимость услуги Промо лот для данного раздела в рублях."
							},
							"params": {
								"$ref": "#/components/schemas/CategoryParams"
							}
						},
						"required": [
							"id",
							"name",
							"parentId",
							"subCategories",
							"params"
						],
						"example": {
							"id": 1798,
							"name": "Другие",
							"parentId": 252,
							"subCategories": false,
							"recommendedPrice": 23,
							"params": {
								"Гарантия подлинности": {
									"type": "options",
									"multi": false,
									"options": {
										"От продавца": 432,
										"Экспертное заключение": 433,
										"Нет": 434
									}
								},
								"Набор": {
									"type": "options",
									"multi": false,
									"options": {
										"Да": 784,
										"Нет": 785
									}
								},
								"Состояние": {
									"type": "options",
									"multi": false,
									"options": {
										"Proof": 5,
										"UNC": 6,
										"AU": 7,
										"XF": 8,
										"VF": 9,
										"F": 10,
										"VG": 11,
										"G": 12,
										"AG": 13,
										"Fair": 14,
										"Basal": 15,
										"VF+": 165,
										"XF+": 166,
										"AU+": 167
									}
								},
								"Металл": {
									"type": "options",
									"multi": false,
									"options": {
										"Золото": 17,
										"Серебро": 18,
										"Бронза": 19,
										"Медь": 20,
										"Медь-Никель": 21,
										"Медь-Цинк": 22,
										"Никель": 23,
										"Алюминий": 24,
										"Железо": 25,
										"Магний": 26,
										"Марганец": 27,
										"Олово": 28,
										"Платина": 29,
										"Хром": 30,
										"Цинк": 31,
										"Алюминиевая Бронза": 32,
										"Аурихалк": 33,
										"Белый Металл": 34,
										"Биллон": 35,
										"Вирениум": 36,
										"Колокольный металл": 37,
										"Коронное золото": 38,
										"Латунь": 39,
										"Никелевая латунь": 40,
										"Никелевое серебро": 41,
										"Нержавеющая сталь": 42,
										"Пинчбек": 43,
										"Пушечный металл": 44,
										"Потин": 45,
										"Пьютер": 46,
										"Спекулум": 47,
										"Сталь": 48,
										"Томпак": 49,
										"Электр": 50,
										"Биметалл": 441,
										"Палладий": 15710,
										"Титан": 16076
									}
								}
							}
						}
					},
					"error": {
						"type": "string",
						"nullable": true,
						"description": "Текст ошибки."
					},
					"errorDetails": {
						"type": "object",
						"nullable": true,
						"additionalProperties": {
							"nullable": true
						},
						"description": "Дополнительные данные ошибки."
					}
				},
				"required": [
					"account",
					"cost",
					"balance",
					"expire",
					"success"
				]
			},
			"CityInfo": {
				"type": "object",
				"properties": {
					"id": {
						"type": "integer",
						"description": "Идентификатор города"
					},
					"name": {
						"type": "string",
						"description": "Название города"
					}
				},
				"required": [
					"id",
					"name"
				],
				"example": {
					"id": 32,
					"name": "Москва"
				}
			},
			"GetCitiesListResponse": {
				"type": "object",
				"properties": {
					"account": {
						"type": "integer",
						"description": "Идентификатор пользователя.",
						"example": 3325567
					},
					"cost": {
						"type": "number",
						"description": "Стоимость выполнения запроса.",
						"example": 1
					},
					"balance": {
						"type": "number",
						"description": "Баланс после выполнения запроса.",
						"example": 29875
					},
					"expire": {
						"type": "number",
						"description": "Время в секундах до истечения действия ключа.",
						"example": 882677
					},
					"success": {
						"type": "integer",
						"description": "Статус выполнения запроса:\n- `1` - успешное выполнение запроса\n- отрицательные значения - ошибка",
						"example": 1
					},
					"result": {
						"type": "array",
						"nullable": true,
						"items": {
							"$ref": "#/components/schemas/CityInfo"
						},
						"example": [
							{
								"id": 1,
								"name": "Абакан1"
							},
							{
								"id": 3,
								"name": "Анапа"
							},
							{
								"id": 5,
								"name": "Белокуриха"
							},
							{
								"id": 6,
								"name": "Брянск"
							},
							{
								"id": 7,
								"name": "Владивосток"
							}
						]
					},
					"error": {
						"type": "string",
						"nullable": true,
						"description": "Текст ошибки."
					},
					"errorDetails": {
						"type": "object",
						"nullable": true,
						"additionalProperties": {
							"nullable": true
						},
						"description": "Дополнительные данные ошибки."
					}
				},
				"required": [
					"account",
					"cost",
					"balance",
					"expire",
					"success"
				]
			},
			"CountryInfo": {
				"type": "object",
				"properties": {
					"id": {
						"type": "integer",
						"description": "Идентификатор страны"
					},
					"name": {
						"type": "string",
						"description": "Название страны"
					}
				},
				"required": [
					"id",
					"name"
				],
				"example": {
					"id": 1,
					"name": "Россия"
				}
			},
			"GetCountryListResponse": {
				"type": "object",
				"properties": {
					"account": {
						"type": "integer",
						"description": "Идентификатор пользователя.",
						"example": 3325567
					},
					"cost": {
						"type": "number",
						"description": "Стоимость выполнения запроса.",
						"example": 1
					},
					"balance": {
						"type": "number",
						"description": "Баланс после выполнения запроса.",
						"example": 29875
					},
					"expire": {
						"type": "number",
						"description": "Время в секундах до истечения действия ключа.",
						"example": 882677
					},
					"success": {
						"type": "integer",
						"description": "Статус выполнения запроса:\n- `1` - успешное выполнение запроса\n- отрицательные значения - ошибка",
						"example": 1
					},
					"result": {
						"type": "array",
						"nullable": true,
						"items": {
							"$ref": "#/components/schemas/CountryInfo"
						},
						"example": [
							{
								"id": 107,
								"name": "Россия"
							},
							{
								"id": 108,
								"name": "Эстония"
							},
							{
								"id": 109,
								"name": "Украина"
							},
							{
								"id": 280,
								"name": "Великобритания"
							},
							{
								"id": 281,
								"name": "О.А.Э."
							},
							{
								"id": 282,
								"name": "Марокко"
							},
							{
								"id": 283,
								"name": "Индия"
							},
							{
								"id": 284,
								"name": "Австралия"
							}
						]
					},
					"error": {
						"type": "string",
						"nullable": true,
						"description": "Текст ошибки."
					},
					"errorDetails": {
						"type": "object",
						"nullable": true,
						"additionalProperties": {
							"nullable": true
						},
						"description": "Дополнительные данные ошибки."
					}
				},
				"required": [
					"account",
					"cost",
					"balance",
					"expire",
					"success"
				]
			},
			"CurrencyInfo": {
				"type": "object",
				"properties": {
					"id": {
						"type": "integer",
						"description": "Идентификатор валюты"
					},
					"symbol": {
						"type": "string",
						"description": "Название валюты по стандарту ISO 4217"
					}
				},
				"required": [
					"id",
					"symbol"
				],
				"example": {
					"id": 2,
					"symbol": "RUB"
				}
			},
			"GetCurrencyListResponse": {
				"type": "object",
				"properties": {
					"account": {
						"type": "integer",
						"description": "Идентификатор пользователя.",
						"example": 3325567
					},
					"cost": {
						"type": "number",
						"description": "Стоимость выполнения запроса.",
						"example": 1
					},
					"balance": {
						"type": "number",
						"description": "Баланс после выполнения запроса.",
						"example": 29875
					},
					"expire": {
						"type": "number",
						"description": "Время в секундах до истечения действия ключа.",
						"example": 882677
					},
					"success": {
						"type": "integer",
						"description": "Статус выполнения запроса:\n- `1` - успешное выполнение запроса\n- отрицательные значения - ошибка",
						"example": 1
					},
					"result": {
						"type": "array",
						"nullable": true,
						"items": {
							"$ref": "#/components/schemas/CurrencyInfo"
						},
						"example": [
							{
								"id": 1,
								"symbol": "USD"
							},
							{
								"id": 2,
								"symbol": "RUB"
							},
							{
								"id": 3,
								"symbol": "EUR"
							},
							{
								"id": 4,
								"symbol": "UAH"
							},
							{
								"id": 5,
								"symbol": "BYN"
							},
							{
								"id": 6,
								"symbol": "KZT"
							},
							{
								"id": 7,
								"symbol": "mBTC"
							}
						]
					},
					"error": {
						"type": "string",
						"nullable": true,
						"description": "Текст ошибки."
					},
					"errorDetails": {
						"type": "object",
						"nullable": true,
						"additionalProperties": {
							"nullable": true
						},
						"description": "Дополнительные данные ошибки."
					}
				},
				"required": [
					"account",
					"cost",
					"balance",
					"expire",
					"success"
				]
			},
			"SubCategoryInfo": {
				"type": "object",
				"properties": {
					"id": {
						"type": "integer",
						"description": "Идентификатор тематического раздела."
					},
					"name": {
						"type": "string",
						"description": "Название тематического раздела."
					},
					"subCategories": {
						"type": "boolean",
						"description": "Наличие подразделов, принимает значения:\n- true - подразделы есть;\n- false - подразделов нет."
					},
					"recommendedPrice": {
						"type": "number",
						"description": "Только для разделов без подразделов: Стоимость услуги \"Промо лот\" для данного раздела в рублях."
					}
				},
				"required": [
					"id",
					"name",
					"subCategories"
				]
			},
			"GetSubCategoryResponse": {
				"type": "object",
				"properties": {
					"account": {
						"type": "integer",
						"description": "Идентификатор пользователя.",
						"example": 3325567
					},
					"cost": {
						"type": "number",
						"description": "Стоимость выполнения запроса.",
						"example": 1
					},
					"balance": {
						"type": "number",
						"description": "Баланс после выполнения запроса.",
						"example": 29875
					},
					"expire": {
						"type": "number",
						"description": "Время в секундах до истечения действия ключа.",
						"example": 882677
					},
					"success": {
						"type": "integer",
						"description": "Статус выполнения запроса:\n- `1` - успешное выполнение запроса\n- отрицательные значения - ошибка",
						"example": 1
					},
					"result": {
						"type": "array",
						"nullable": true,
						"items": {
							"$ref": "#/components/schemas/SubCategoryInfo"
						},
						"example": [
							{
								"id": 2401,
								"name": "Патефоны, граммофоны",
								"subCategories": true
							},
							{
								"id": 14106,
								"name": "Другие",
								"subCategories": false,
								"recommendedPrice": 13
							},
							{
								"id": 14107,
								"name": "Аккордеоны, баяны, гармони",
								"subCategories": false,
								"recommendedPrice": 13
							},
							{
								"id": 14108,
								"name": "Губные гармошки",
								"subCategories": false,
								"recommendedPrice": 13
							},
							{
								"id": 14109,
								"name": "Духовые инструменты",
								"subCategories": false,
								"recommendedPrice": 13
							},
							{
								"id": 14110,
								"name": "Клавишные инструменты",
								"subCategories": false,
								"recommendedPrice": 13
							},
							{
								"id": 14111,
								"name": "Колокольчики и колокола",
								"subCategories": false,
								"recommendedPrice": 13
							},
							{
								"id": 14112,
								"name": "Музыкальные шкатулки, полифоны",
								"subCategories": false,
								"recommendedPrice": 13
							},
							{
								"id": 14113,
								"name": "Струнные инструменты",
								"subCategories": false,
								"recommendedPrice": 13
							},
							{
								"id": 14114,
								"name": "Литература, каталоги",
								"subCategories": false,
								"recommendedPrice": 13
							}
						]
					},
					"error": {
						"type": "string",
						"nullable": true,
						"description": "Текст ошибки."
					},
					"errorDetails": {
						"type": "object",
						"nullable": true,
						"additionalProperties": {
							"nullable": true
						},
						"description": "Дополнительные данные ошибки."
					}
				},
				"required": [
					"account",
					"cost",
					"balance",
					"expire",
					"success"
				]
			},
			"DeleteItemResponse": {
				"type": "object",
				"properties": {
					"account": {
						"type": "integer",
						"description": "Идентификатор пользователя.",
						"example": 3325567
					},
					"cost": {
						"type": "number",
						"description": "Стоимость выполнения запроса.",
						"example": 1
					},
					"balance": {
						"type": "number",
						"description": "Баланс после выполнения запроса.",
						"example": 29875
					},
					"expire": {
						"type": "number",
						"description": "Время в секундах до истечения действия ключа.",
						"example": 882677
					},
					"success": {
						"type": "integer",
						"description": "Статус выполнения запроса:\n- `1` - успешное выполнение запроса\n- отрицательные значения - ошибка",
						"example": 1
					},
					"result": {
						"type": "object",
						"nullable": true,
						"properties": {
							"id": {
								"type": "integer",
								"description": "Идентификатор."
							}
						},
						"required": [
							"id"
						],
						"example": {
							"id": 123456789
						}
					},
					"error": {
						"type": "string",
						"nullable": true,
						"description": "Текст ошибки."
					},
					"errorDetails": {
						"type": "object",
						"nullable": true,
						"additionalProperties": {
							"nullable": true
						},
						"description": "Дополнительные данные ошибки."
					}
				},
				"required": [
					"account",
					"cost",
					"balance",
					"expire",
					"success"
				]
			},
			"CommonDescription": {
				"type": "object",
				"properties": {
					"id": {
						"type": "integer",
						"description": "Идентификатор"
					},
					"name": {
						"type": "string",
						"description": "Название"
					},
					"commonDescription": {
						"type": "string",
						"description": "Общее описание"
					}
				},
				"required": [
					"id",
					"name",
					"commonDescription"
				]
			},
			"GetCommonDescriptionListResponse": {
				"type": "object",
				"properties": {
					"account": {
						"type": "integer",
						"description": "Идентификатор пользователя.",
						"example": 3325567
					},
					"cost": {
						"type": "number",
						"description": "Стоимость выполнения запроса.",
						"example": 1
					},
					"balance": {
						"type": "number",
						"description": "Баланс после выполнения запроса.",
						"example": 29875
					},
					"expire": {
						"type": "number",
						"description": "Время в секундах до истечения действия ключа.",
						"example": 882677
					},
					"success": {
						"type": "integer",
						"description": "Статус выполнения запроса:\n- `1` - успешное выполнение запроса\n- отрицательные значения - ошибка",
						"example": 1
					},
					"result": {
						"type": "array",
						"nullable": true,
						"items": {
							"$ref": "#/components/schemas/CommonDescription"
						},
						"example": [
							{
								"id": 2234,
								"name": "Yellow penguin",
								"commonDescription": "Yellow penguin description text"
							},
							{
								"id": 2235,
								"name": "Green penguin",
								"commonDescription": "Green penguin description text"
							}
						]
					},
					"error": {
						"type": "string",
						"nullable": true,
						"description": "Текст ошибки."
					},
					"errorDetails": {
						"type": "object",
						"nullable": true,
						"additionalProperties": {
							"nullable": true
						},
						"description": "Дополнительные данные ошибки."
					}
				},
				"required": [
					"account",
					"cost",
					"balance",
					"expire",
					"success"
				]
			},
			"LotFinished": {
				"type": "object",
				"properties": {
					"id": {
						"type": "integer",
						"description": "Номер лота."
					},
					"internalId": {
						"type": "string",
						"description": "Артикул."
					},
					"newId": {
						"type": "integer",
						"description": "Новый id лота, если лот перевыставлен на повторные торги."
					},
					"endDateTime": {
						"type": "string",
						"description": "Дата и время окончания торгов в формате 'YYYY-MM-DD HH:MM:SS'.\nNA - отсутствие даты окончания."
					},
					"TZ": {
						"type": "string",
						"description": "Часовой пояс."
					}
				},
				"required": [
					"id",
					"endDateTime",
					"TZ"
				]
			},
			"GetFinishedItemListResponse": {
				"type": "object",
				"properties": {
					"account": {
						"type": "integer",
						"description": "Идентификатор пользователя.",
						"example": 3325567
					},
					"cost": {
						"type": "number",
						"description": "Стоимость выполнения запроса.",
						"example": 1
					},
					"balance": {
						"type": "number",
						"description": "Баланс после выполнения запроса.",
						"example": 29875
					},
					"expire": {
						"type": "number",
						"description": "Время в секундах до истечения действия ключа.",
						"example": 882677
					},
					"success": {
						"type": "integer",
						"description": "Статус выполнения запроса:\n- `1` - успешное выполнение запроса\n- отрицательные значения - ошибка",
						"example": 1
					},
					"result": {
						"type": "array",
						"nullable": true,
						"items": {
							"$ref": "#/components/schemas/LotFinished"
						},
						"example": [
							{
								"id": 290730300,
								"internalId": "s-12949",
								"endDateTime": "2025-06-19 11:15:00",
								"TZ": "MSK"
							}
						]
					},
					"error": {
						"type": "string",
						"nullable": true,
						"description": "Текст ошибки."
					},
					"errorDetails": {
						"type": "object",
						"nullable": true,
						"additionalProperties": {
							"nullable": true
						},
						"description": "Дополнительные данные ошибки."
					}
				},
				"required": [
					"account",
					"cost",
					"balance",
					"expire",
					"success"
				]
			},
			"Longevity": {
				"type": "integer",
				"enum": [
					3,
					4,
					5,
					6,
					7,
					8,
					9,
					10,
					11,
					12,
					13,
					14,
					15,
					16,
					17,
					18,
					19,
					20,
					21,
					30,
					100
				],
				"description": "Продолжительность торгов в днях.\n\nДля аукционов может принимать значения от 3 до 21.\nДля лотов с фиксированной ценой 30 и 100, где 100 означает без срока окончания.\nВыставление без срока окончания доступно только для подписчиков на т.п. Продавец.",
				"example": 7
			},
			"Payment": {
				"type": "string",
				"description": "Способы оплаты за лот. Один или несколько способов оплаты за лот из списка через запятую:\n- `CASH` - наличные;\n- `BANK` - банковский перевод;\n- `NALOZH` - наложенный платеж почты РФ;\n- `CARD` - перевод на банковскую карту;\n- `YANDEX` - ЮMoney (Яндекс.Деньги);\n- `GOLDCROWN` - Золотая Корона;\n- `OZON` - Ozon Безопасна сделка;\n- `SBP` - СБП;\n- `WB` - WB Track;\n- `MAIL` - Почтовый перевод;\n- `PAYPAL` - PayPal;\n- `BITCOIN` - Биткоины;\n- `DESC` - подробности в описании.",
				"example": "CASH,BANK,CARD,DESC"
			},
			"LotCondition": {
				"type": "string",
				"enum": [
					"used",
					"new",
					"NA"
				],
				"description": "Состояние лота"
			},
			"LotDelivery": {
				"type": "object",
				"properties": {
					"delivery": {
						"type": "string",
						"enum": [
							"NO",
							"COUNTRY",
							"WORLD"
						],
						"description": "Доставляется ли лот за пределы города:\n- `NO` - не доставляется;\n- `COUNTRY` - доставляется по стране;\n- `WORLD` - доставляется по стране и миру.",
						"example": "WORLD"
					},
					"localDelivery": {
						"type": "string",
						"enum": [
							"SELF",
							"FREE",
							"CHARGE"
						],
						"description": "Доставка по городу (одно из значений):\n- `SELF` - самовывоз;\n- `FREE` - бесплатно;\n- `CHARGE` - за плату.",
						"example": "CHARGE"
					},
					"localDeliveryPrice": {
						"type": "number",
						"nullable": true,
						"description": "Стоимость доставки по городу.\n- `0.00` - уточняйте стоимость доставки дополнительно;\n- Число больше 0 - стоимость в валюте цены.",
						"example": 50
					},
					"countryDeliveryPrice": {
						"type": "number",
						"nullable": true,
						"description": "Стоимость доставки по стране.\n- `-1` - бесплатно;\n- `0.00` - уточняйте стоимость доставки дополнительно;\n- Число больше 0 - стоимость в валюте цены.",
						"example": 100
					},
					"worldDeliveryPrice": {
						"type": "number",
						"nullable": true,
						"description": "Стоимость доставки по миру.\n- `-1` - бесплатно;\n- `0.00` - уточняйте стоимость доставки дополнительно;\n- Число больше 0 - стоимость в валюте цены.",
						"example": 150
					}
				},
				"required": [
					"delivery",
					"localDelivery"
				]
			},
			"LotInfoCommonNoDelivery": {
				"allOf": [
					{
						"$ref": "#/components/schemas/LotDelivery"
					},
					{
						"type": "object",
						"properties": {
							"id": {
								"type": "integer",
								"description": "Идентификатор лота."
							},
							"name": {
								"type": "string",
								"description": "Название лота."
							},
							"category": {
								"type": "integer",
								"description": "Идентификатор категории."
							},
							"longevity": {
								"$ref": "#/components/schemas/Longevity"
							},
							"listDateTime": {
								"type": "string",
								"description": "Дата и время выставления лота на продажу в формате 'YYYY-MM-DD HH:MM:SS'."
							},
							"endDateTime": {
								"type": "string",
								"description": "Дата и время окончания торгов в формате 'YYYY-MM-DD HH:MM:SS'.\nNA - отсутствие даты окончания."
							},
							"curencyId": {
								"type": "integer",
								"description": "Идентификатор валюты."
							},
							"quantity": {
								"type": "integer",
								"description": "Количество."
							},
							"tags": {
								"type": "string",
								"description": "Метки лота через запятую."
							},
							"categoryParams": {
								"type": "string",
								"description": "Номера дополнительных параметров лота через запятую."
							},
							"bold": {
								"allOf": [
									{
										"$ref": "#/components/schemas/YesOrNo"
									},
									{
										"description": "Выделение жирным в списке лотов"
									}
								]
							},
							"recommended": {
								"allOf": [
									{
										"$ref": "#/components/schemas/YesOrNo"
									},
									{
										"description": "Промо лот"
									}
								]
							},
							"payment": {
								"$ref": "#/components/schemas/Payment"
							},
							"minimalBuyerRate": {
								"type": "integer",
								"description": "Минимальный рейтинг покупателя. Число в диапазоне от 0 до 9."
							},
							"condition": {
								"$ref": "#/components/schemas/LotCondition"
							},
							"numberOfPictures": {
								"type": "integer",
								"description": "Количество изображений лота."
							},
							"city": {
								"type": "integer",
								"description": "Идентификатор города, в котором расположен лот."
							},
							"status": {
								"type": "string",
								"enum": [
									"deferred",
									"draft",
									"listed",
									"finished",
									"deleted"
								],
								"description": "Статус лота.\ndeferred - отложенный старт\ndraft - черновик\nlisted - на продаже\nfinished - торги завершены\ndeleted - удален."
							},
							"newId": {
								"type": "integer",
								"description": "Новый id лота, если лот перевыставлен на повторные торги."
							},
							"deliveryText": {
								"type": "string",
								"description": "Дополнительная информация о доставке и оплате. Текст до 1000 символов."
							},
							"description": {
								"type": "string",
								"description": "Описание лота. Может быть использован HTML."
							},
							"commonDescriptions": {
								"type": "string",
								"description": "Список идентификаторов стандартных описаний для лота через запятую."
							},
							"TZ": {
								"type": "string",
								"description": "Часовой пояс."
							},
							"internalId": {
								"type": "string",
								"description": "Артикул. Идентификатор лота в вашей системе."
							}
						},
						"required": [
							"id",
							"name",
							"category",
							"longevity",
							"listDateTime",
							"endDateTime",
							"curencyId",
							"quantity",
							"tags",
							"categoryParams",
							"bold",
							"recommended",
							"payment",
							"minimalBuyerRate",
							"condition",
							"numberOfPictures",
							"city",
							"status",
							"deliveryText",
							"description",
							"commonDescriptions",
							"TZ"
						]
					}
				]
			},
			"LotAuctionInfo": {
				"allOf": [
					{
						"$ref": "#/components/schemas/LotInfoCommonNoDelivery"
					},
					{
						"type": "object",
						"properties": {
							"saleType": {
								"type": "string",
								"enum": [
									"Auction"
								],
								"description": "Тип продажи."
							},
							"startPrice": {
								"type": "number",
								"description": "Для аукционов: начальная цена.",
								"example": 1
							},
							"currentPrice": {
								"type": "number",
								"description": "Для аукционов: текущая цена.",
								"example": 45.9
							},
							"strikePrice": {
								"type": "number",
								"description": "Для аукционов: цена Купить сейчас.",
								"example": 250
							},
							"bids": {
								"type": "integer",
								"description": "Для аукционов: Количество ставок.",
								"example": 24
							},
							"antisniper": {
								"allOf": [
									{
										"$ref": "#/components/schemas/YesOrNo"
									},
									{
										"description": "Для аукционов: Опция автопродления.",
										"example": "Y"
									}
								]
							},
							"notify": {
								"allOf": [
									{
										"$ref": "#/components/schemas/YesOrNo"
									},
									{
										"description": "Для аукционов: флаг оповещения о новый ставках на лот",
										"example": "Y"
									}
								]
							},
							"prolong": {
								"type": "integer",
								"description": "Для аукционов: количество продлений в случае неудачных торгов.",
								"example": 10
							},
							"prolongDone": {
								"type": "integer",
								"description": "Для аукционов: количество осуществленных продлений.",
								"example": 3
							}
						},
						"required": [
							"saleType",
							"bids"
						]
					}
				]
			},
			"LotFixedInfo": {
				"allOf": [
					{
						"$ref": "#/components/schemas/LotInfoCommonNoDelivery"
					},
					{
						"type": "object",
						"properties": {
							"saleType": {
								"type": "string",
								"enum": [
									"Sale"
								],
								"description": "Тип продажи."
							},
							"price": {
								"type": "number",
								"description": "Цена - для товаров с фиксированной ценой.",
								"example": 100
							},
							"bestOffer": {
								"allOf": [
									{
										"$ref": "#/components/schemas/YesOrNo"
									},
									{
										"description": "Для лотов с фиксированной ценой: Торг уместен.",
										"example": "Y"
									}
								]
							},
							"sold": {
								"type": "integer",
								"description": "Количество проданных экземпляров лота для товаров с фиксированной ценой.",
								"example": 4
							}
						},
						"required": [
							"saleType"
						]
					}
				]
			},
			"GetItemInfoResponse": {
				"type": "object",
				"properties": {
					"account": {
						"type": "integer",
						"description": "Идентификатор пользователя.",
						"example": 3325567
					},
					"cost": {
						"type": "number",
						"description": "Стоимость выполнения запроса.",
						"example": 1
					},
					"balance": {
						"type": "number",
						"description": "Баланс после выполнения запроса.",
						"example": 29875
					},
					"expire": {
						"type": "number",
						"description": "Время в секундах до истечения действия ключа.",
						"example": 882677
					},
					"success": {
						"type": "integer",
						"description": "Статус выполнения запроса:\n- `1` - успешное выполнение запроса\n- отрицательные значения - ошибка",
						"example": 1
					},
					"result": {
						"oneOf": [
							{
								"$ref": "#/components/schemas/LotAuctionInfo"
							},
							{
								"$ref": "#/components/schemas/LotFixedInfo"
							},
							{
								"nullable": true
							}
						],
						"examples": [
							{
								"price": 100,
								"bestOffer": "Y",
								"sold": 0,
								"id": 290730300,
								"internalId": "s-12949",
								"name": "Красный пингвин",
								"category": 1488,
								"saleType": "Sale",
								"longevity": 30,
								"listDateTime": "2025-05-20 11:15:00",
								"endDateTime": "2025-06-19 11:15:00",
								"curencyId": 2,
								"quantity": 2,
								"tags": "one,two,three",
								"categoryParams": "",
								"bold": "Y",
								"recommended": "Y",
								"payment": "CASH,OZON",
								"localDelivery": "SELF",
								"localDeliveryPrice": 0,
								"delivery": "WORLD",
								"countryDeliveryPrice": -1,
								"worldDeliveryPrice": -1,
								"minimalBuyerRate": 3,
								"condition": "used",
								"numberOfPictures": 1,
								"city": 32,
								"status": "listed",
								"deliveryText": "Add Delivery",
								"description": "Cool",
								"commonDescriptions": "21504,21505",
								"TZ": "MSK"
							},
							{
								"startPrice": 45,
								"currentPrice": 45,
								"strikePrice": 5433,
								"bids": 0,
								"id": 290730308,
								"name": "Червяк Анатолий",
								"category": 12455,
								"saleType": "Auction",
								"longevity": 7,
								"listDateTime": "2025-06-05 20:32:00",
								"endDateTime": "2025-06-12 20:32:00",
								"curencyId": 2,
								"quantity": 1,
								"tags": "",
								"categoryParams": "",
								"bold": "N",
								"recommended": "N",
								"payment": "CASH,WB,DESC",
								"localDelivery": "SELF",
								"localDeliveryPrice": 0,
								"delivery": "NO",
								"countryDeliveryPrice": 0,
								"worldDeliveryPrice": 0,
								"minimalBuyerRate": 3,
								"condition": "new",
								"numberOfPictures": 0,
								"city": 32,
								"status": "listed",
								"deliveryText": "Add Delivery",
								"description": "<div class=\"user_content_v1\"><p>WORMS WORLD PARTY!</p></div>",
								"commonDescriptions": "",
								"TZ": "MSK"
							}
						]
					},
					"error": {
						"type": "string",
						"nullable": true,
						"description": "Текст ошибки."
					},
					"errorDetails": {
						"type": "object",
						"nullable": true,
						"additionalProperties": {
							"nullable": true
						},
						"description": "Дополнительные данные ошибки."
					}
				},
				"required": [
					"account",
					"cost",
					"balance",
					"expire",
					"success"
				]
			},
			"LotOnSaleAuction": {
				"type": "object",
				"properties": {
					"id": {
						"type": "integer",
						"description": "Номер лота."
					},
					"internalId": {
						"type": "string",
						"description": "Артикул."
					},
					"saleType": {
						"type": "string",
						"enum": [
							"Auction"
						],
						"description": "Тип продажи."
					},
					"bids": {
						"type": "integer",
						"description": "Количество ставок."
					},
					"currentPrice": {
						"type": "number",
						"description": "Текущая цена для аукционов."
					},
					"curencyId": {
						"type": "integer",
						"description": "Идентификатор валюты."
					}
				},
				"required": [
					"id",
					"saleType",
					"bids",
					"currentPrice",
					"curencyId"
				]
			},
			"LotOnSaleFixed": {
				"type": "object",
				"properties": {
					"id": {
						"type": "integer",
						"description": "Номер лота."
					},
					"internalId": {
						"type": "string",
						"description": "Артикул."
					},
					"saleType": {
						"type": "string",
						"enum": [
							"Sale"
						],
						"description": "Тип продажи."
					},
					"quantity": {
						"type": "integer",
						"description": "Количество лотов."
					},
					"sold": {
						"type": "integer",
						"description": "Количество проданных лотов."
					},
					"price": {
						"type": "number",
						"description": "Цена лота."
					},
					"curencyId": {
						"type": "integer",
						"description": "Идентификатор валюты."
					}
				},
				"required": [
					"id",
					"saleType",
					"quantity",
					"sold",
					"price",
					"curencyId"
				]
			},
			"LotOnSale": {
				"anyOf": [
					{
						"$ref": "#/components/schemas/LotOnSaleAuction"
					},
					{
						"$ref": "#/components/schemas/LotOnSaleFixed"
					}
				]
			},
			"GetItemListResponse": {
				"type": "object",
				"properties": {
					"account": {
						"type": "integer",
						"description": "Идентификатор пользователя.",
						"example": 3325567
					},
					"cost": {
						"type": "number",
						"description": "Стоимость выполнения запроса.",
						"example": 1
					},
					"balance": {
						"type": "number",
						"description": "Баланс после выполнения запроса.",
						"example": 29875
					},
					"expire": {
						"type": "number",
						"description": "Время в секундах до истечения действия ключа.",
						"example": 882677
					},
					"success": {
						"type": "integer",
						"description": "Статус выполнения запроса:\n- `1` - успешное выполнение запроса\n- отрицательные значения - ошибка",
						"example": 1
					},
					"result": {
						"type": "array",
						"nullable": true,
						"items": {
							"$ref": "#/components/schemas/LotOnSale"
						},
						"example": [
							{
								"id": 290730300,
								"internalId": "s-12949",
								"saleType": "Sale",
								"quantity": 2,
								"curencyId": 2,
								"price": 100,
								"sold": 0
							},
							{
								"id": 290730308,
								"internalId": "s-12950",
								"saleType": "Auction",
								"bids": 0,
								"curencyId": 2,
								"currentPrice": 100
							}
						]
					},
					"error": {
						"type": "string",
						"nullable": true,
						"description": "Текст ошибки."
					},
					"errorDetails": {
						"type": "object",
						"nullable": true,
						"additionalProperties": {
							"nullable": true
						},
						"description": "Дополнительные данные ошибки."
					}
				},
				"required": [
					"account",
					"cost",
					"balance",
					"expire",
					"success"
				]
			},
			"SoldLotFinished": {
				"allOf": [
					{
						"$ref": "#/components/schemas/LotFinished"
					},
					{
						"type": "object",
						"properties": {
							"orderId": {
								"type": "integer",
								"description": "Номер сделки."
							},
							"quantity": {
								"type": "integer",
								"description": "Количество лотов в сделке."
							},
							"happend": {
								"type": "string",
								"nullable": true,
								"enum": [
									"Y",
									"N"
								],
								"description": "Флаг состояния сделки. Возможные значения:\nnull - статус сделки не определен продавцом;\nY - сделка состоялась;\nN - сделка не состоялась;"
							}
						},
						"required": [
							"orderId",
							"quantity",
							"happend"
						]
					}
				]
			},
			"GetSoldFinishedItemListResponse": {
				"type": "object",
				"properties": {
					"account": {
						"type": "integer",
						"description": "Идентификатор пользователя.",
						"example": 3325567
					},
					"cost": {
						"type": "number",
						"description": "Стоимость выполнения запроса.",
						"example": 1
					},
					"balance": {
						"type": "number",
						"description": "Баланс после выполнения запроса.",
						"example": 29875
					},
					"expire": {
						"type": "number",
						"description": "Время в секундах до истечения действия ключа.",
						"example": 882677
					},
					"success": {
						"type": "integer",
						"description": "Статус выполнения запроса:\n- `1` - успешное выполнение запроса\n- отрицательные значения - ошибка",
						"example": 1
					},
					"result": {
						"type": "array",
						"nullable": true,
						"items": {
							"$ref": "#/components/schemas/SoldLotFinished"
						},
						"example": [
							{
								"orderId": 1234567890,
								"quantity": 1,
								"happend": "Y",
								"id": 290730300,
								"internalId": "s-12949",
								"endDateTime": "2025-06-19 11:15:00",
								"TZ": "MSK"
							}
						]
					},
					"error": {
						"type": "string",
						"nullable": true,
						"description": "Текст ошибки."
					},
					"errorDetails": {
						"type": "object",
						"nullable": true,
						"additionalProperties": {
							"nullable": true
						},
						"description": "Дополнительные данные ошибки."
					}
				},
				"required": [
					"account",
					"cost",
					"balance",
					"expire",
					"success"
				]
			},
			"GetUnsoldFinishedItemListResponse": {
				"type": "object",
				"properties": {
					"account": {
						"type": "integer",
						"description": "Идентификатор пользователя.",
						"example": 3325567
					},
					"cost": {
						"type": "number",
						"description": "Стоимость выполнения запроса.",
						"example": 1
					},
					"balance": {
						"type": "number",
						"description": "Баланс после выполнения запроса.",
						"example": 29875
					},
					"expire": {
						"type": "number",
						"description": "Время в секундах до истечения действия ключа.",
						"example": 882677
					},
					"success": {
						"type": "integer",
						"description": "Статус выполнения запроса:\n- `1` - успешное выполнение запроса\n- отрицательные значения - ошибка",
						"example": 1
					},
					"result": {
						"type": "array",
						"nullable": true,
						"items": {
							"$ref": "#/components/schemas/LotFinished"
						},
						"example": [
							{
								"id": 290730300,
								"internalId": "s-12949",
								"endDateTime": "2025-06-19 11:15:00",
								"TZ": "MSK",
								"newId": 290730301
							}
						]
					},
					"error": {
						"type": "string",
						"nullable": true,
						"description": "Текст ошибки."
					},
					"errorDetails": {
						"type": "object",
						"nullable": true,
						"additionalProperties": {
							"nullable": true
						},
						"description": "Дополнительные данные ошибки."
					}
				},
				"required": [
					"account",
					"cost",
					"balance",
					"expire",
					"success"
				]
			},
			"BillingResult": {
				"type": "object",
				"properties": {
					"success": {
						"type": "integer",
						"description": "0 - услуга не оказана;\n1 - услуга оказана;"
					},
					"html": {
						"type": "string",
						"description": "Текстовое описание ошибки или сообщения об успешном оказании услуги в HTML формате."
					},
					"price": {
						"type": "number",
						"description": "Стоимость оказанной услуги в рублях."
					}
				},
				"required": [
					"success",
					"html",
					"price"
				]
			},
			"ListItemResponse": {
				"type": "object",
				"properties": {
					"account": {
						"type": "integer",
						"description": "Идентификатор пользователя.",
						"example": 3325567
					},
					"cost": {
						"type": "number",
						"description": "Стоимость выполнения запроса.",
						"example": 1
					},
					"balance": {
						"type": "number",
						"description": "Баланс после выполнения запроса.",
						"example": 29875
					},
					"expire": {
						"type": "number",
						"description": "Время в секундах до истечения действия ключа.",
						"example": 882677
					},
					"success": {
						"type": "integer",
						"description": "Статус выполнения запроса:\n- `1` - успешное выполнение запроса\n- отрицательные значения - ошибка",
						"example": 1
					},
					"result": {
						"type": "object",
						"nullable": true,
						"properties": {
							"id": {
								"type": "integer",
								"description": "Номер нового лота."
							},
							"endDateTime": {
								"type": "string",
								"description": "Дата и время окончания торгов в формате 'YYYY-MM-DD HH:MM:SS'.\nNA - отсутствие даты окончания."
							},
							"billing": {
								"type": "object",
								"properties": {
									"bold": {
										"$ref": "#/components/schemas/BillingResult"
									},
									"recommended": {
										"$ref": "#/components/schemas/BillingResult"
									}
								},
								"description": "Информация об оказанных платных услугах."
							},
							"TZ": {
								"type": "string",
								"description": "Часовой пояс."
							}
						},
						"required": [
							"id",
							"endDateTime",
							"billing",
							"TZ"
						],
						"example": {
							"id": 290730300,
							"endDateTime": "2025-06-19 11:15:00",
							"billing": {
								"bold": {
									"success": 1,
									"html": "Услуга Жирный шрифт для лота 290730309 добавлена. С Вашего счета списано 9 р.",
									"price": 9
								}
							},
							"TZ": "MSK"
						}
					},
					"error": {
						"type": "string",
						"nullable": true,
						"description": "Текст ошибки."
					},
					"errorDetails": {
						"type": "object",
						"nullable": true,
						"additionalProperties": {
							"nullable": true
						},
						"description": "Дополнительные данные ошибки."
					}
				},
				"required": [
					"account",
					"cost",
					"balance",
					"expire",
					"success"
				]
			},
			"RelistItemResponse": {
				"type": "object",
				"properties": {
					"account": {
						"type": "integer",
						"description": "Идентификатор пользователя.",
						"example": 3325567
					},
					"cost": {
						"type": "number",
						"description": "Стоимость выполнения запроса.",
						"example": 1
					},
					"balance": {
						"type": "number",
						"description": "Баланс после выполнения запроса.",
						"example": 29875
					},
					"expire": {
						"type": "number",
						"description": "Время в секундах до истечения действия ключа.",
						"example": 882677
					},
					"success": {
						"type": "integer",
						"description": "Статус выполнения запроса:\n- `1` - успешное выполнение запроса\n- отрицательные значения - ошибка",
						"example": 1
					},
					"result": {
						"type": "object",
						"nullable": true,
						"properties": {
							"id": {
								"type": "integer",
								"description": "Идентификатор."
							}
						},
						"required": [
							"id"
						],
						"example": {
							"id": 290730301
						}
					},
					"error": {
						"type": "string",
						"nullable": true,
						"description": "Текст ошибки."
					},
					"errorDetails": {
						"type": "object",
						"nullable": true,
						"additionalProperties": {
							"nullable": true
						},
						"description": "Дополнительные данные ошибки."
					}
				},
				"required": [
					"account",
					"cost",
					"balance",
					"expire",
					"success"
				]
			},
			"StopSaleResponse": {
				"type": "object",
				"properties": {
					"account": {
						"type": "integer",
						"description": "Идентификатор пользователя.",
						"example": 3325567
					},
					"cost": {
						"type": "number",
						"description": "Стоимость выполнения запроса.",
						"example": 1
					},
					"balance": {
						"type": "number",
						"description": "Баланс после выполнения запроса.",
						"example": 29875
					},
					"expire": {
						"type": "number",
						"description": "Время в секундах до истечения действия ключа.",
						"example": 882677
					},
					"success": {
						"type": "integer",
						"description": "Статус выполнения запроса:\n- `1` - успешное выполнение запроса\n- отрицательные значения - ошибка",
						"example": 1
					},
					"result": {
						"type": "object",
						"nullable": true,
						"properties": {
							"id": {
								"type": "integer",
								"description": "Идентификатор."
							}
						},
						"required": [
							"id"
						],
						"example": {
							"id": 290730300
						}
					},
					"error": {
						"type": "string",
						"nullable": true,
						"description": "Текст ошибки."
					},
					"errorDetails": {
						"type": "object",
						"nullable": true,
						"additionalProperties": {
							"nullable": true
						},
						"description": "Дополнительные данные ошибки."
					}
				},
				"required": [
					"account",
					"cost",
					"balance",
					"expire",
					"success"
				]
			},
			"UpdateItemResponse": {
				"type": "object",
				"properties": {
					"account": {
						"type": "integer",
						"description": "Идентификатор пользователя.",
						"example": 3325567
					},
					"cost": {
						"type": "number",
						"description": "Стоимость выполнения запроса.",
						"example": 1
					},
					"balance": {
						"type": "number",
						"description": "Баланс после выполнения запроса.",
						"example": 29875
					},
					"expire": {
						"type": "number",
						"description": "Время в секундах до истечения действия ключа.",
						"example": 882677
					},
					"success": {
						"type": "integer",
						"description": "Статус выполнения запроса:\n- `1` - успешное выполнение запроса\n- отрицательные значения - ошибка",
						"example": 1
					},
					"result": {
						"type": "object",
						"nullable": true,
						"properties": {
							"id": {
								"type": "integer",
								"description": "Номер нового лота."
							},
							"endDateTime": {
								"type": "string",
								"description": "Дата и время окончания торгов в формате 'YYYY-MM-DD HH:MM:SS'.\nNA - отсутствие даты окончания."
							},
							"billing": {
								"type": "object",
								"properties": {
									"bold": {
										"$ref": "#/components/schemas/BillingResult"
									},
									"recommended": {
										"$ref": "#/components/schemas/BillingResult"
									}
								},
								"description": "Информация об оказанных платных услугах."
							},
							"TZ": {
								"type": "string",
								"description": "Часовой пояс."
							}
						},
						"required": [
							"id",
							"endDateTime",
							"billing",
							"TZ"
						],
						"example": {
							"id": 290730300,
							"endDateTime": "2025-06-19 11:15:00",
							"billing": {
								"bold": {
									"success": 1,
									"html": "Услуга Жирный шрифт для лота 290730309 добавлена. С Вашего счета списано 9 р.",
									"price": 9
								}
							},
							"TZ": "MSK"
						}
					},
					"error": {
						"type": "string",
						"nullable": true,
						"description": "Текст ошибки."
					},
					"errorDetails": {
						"type": "object",
						"nullable": true,
						"additionalProperties": {
							"nullable": true
						},
						"description": "Дополнительные данные ошибки."
					}
				},
				"required": [
					"account",
					"cost",
					"balance",
					"expire",
					"success"
				]
			},
			"GetCategoryInfoRequest": {
				"type": "object",
				"properties": {
					"id": {
						"type": "integer",
						"nullable": true,
						"description": "Идентификатор тематического раздела."
					}
				},
				"required": [
					"id"
				]
			},
			"GetCitiesListRequest": {
				"type": "object",
				"properties": {
					"id": {
						"type": "integer",
						"nullable": true,
						"description": "Идентификатор страны."
					}
				},
				"required": [
					"id"
				]
			},
			"GetSubCategoryRequest": {
				"type": "object",
				"properties": {
					"id": {
						"type": "integer",
						"nullable": true,
						"description": "Идентификатор родительского раздела."
					}
				},
				"required": [
					"id"
				]
			},
			"DeleteItemRequest": {
				"type": "object",
				"properties": {
					"id": {
						"type": "integer",
						"nullable": true,
						"description": "Номер лота."
					}
				},
				"required": [
					"id"
				]
			},
			"GetCommonDescriptionListRequest": {
				"type": "object",
				"properties": {}
			},
			"GetFinishedItemListRequest": {
				"type": "object",
				"properties": {}
			},
			"GetItemInfoRequest": {
				"type": "object",
				"properties": {
					"id": {
						"type": "integer",
						"minimum": 0,
						"exclusiveMinimum": true,
						"description": "Идентификатор лота."
					}
				},
				"required": [
					"id"
				]
			},
			"GetItemListRequest": {
				"type": "object",
				"properties": {}
			},
			"GetSoldFinishedItemListRequest": {
				"type": "object",
				"properties": {}
			},
			"GetUnsoldFinishedItemListRequest": {
				"type": "object",
				"properties": {}
			},
			"NewLotCommon": {
				"allOf": [
					{
						"$ref": "#/components/schemas/LotDelivery"
					},
					{
						"type": "object",
						"properties": {
							"name": {
								"type": "string",
								"description": "Наименование лота."
							},
							"category": {
								"type": "integer",
								"nullable": true,
								"description": "Номер тематического раздела"
							},
							"longevity": {
								"$ref": "#/components/schemas/Longevity"
							},
							"listDateTime": {
								"type": "string",
								"description": "Дата и время выставления лота на продажу в формате 'YYYY-MM-DD HH:MM:SS'.\nПринимаются к размещению лоты, чья дата размещения отстоит от текущего времени не более чем на 30 дней."
							},
							"curencyId": {
								"type": "integer",
								"nullable": true,
								"description": "Идентификатор валюты."
							},
							"tags": {
								"type": "string",
								"description": "Метки лота через запятую."
							},
							"categoryParams": {
								"type": "string",
								"description": "Номера дополнительных параметров лота через запятую."
							},
							"bold": {
								"allOf": [
									{
										"$ref": "#/components/schemas/YesOrNo"
									},
									{
										"description": "Выделение жирным в списке лотов"
									}
								]
							},
							"recommended": {
								"allOf": [
									{
										"$ref": "#/components/schemas/YesOrNo"
									},
									{
										"description": "Промо лот"
									}
								]
							},
							"payment": {
								"$ref": "#/components/schemas/Payment"
							},
							"minimalBuyerRate": {
								"type": "integer",
								"nullable": true,
								"description": "Минимальный рейтинг покупателя. Число в диапазоне от 0 до 9."
							},
							"condition": {
								"$ref": "#/components/schemas/LotCondition"
							},
							"pictures": {
								"type": "string",
								"description": "Список URL изображений лота через запятую."
							},
							"city": {
								"type": "integer",
								"nullable": true,
								"description": "Идентификатор города, в котором расположен лот."
							},
							"deliveryText": {
								"type": "string",
								"description": "Дополнительная информация о доставке и оплате. Текст до 1000 символов."
							},
							"description": {
								"type": "string",
								"description": "Описание лота. Может быть использован HTML."
							},
							"commonDescriptions": {
								"type": "string",
								"description": "Список идентификаторов стандартных описаний для лота через запятую."
							},
							"internalId": {
								"type": "string",
								"description": "Артикул. Идентификатор лота в вашей системе."
							}
						},
						"required": [
							"name",
							"category",
							"longevity",
							"curencyId",
							"payment",
							"city",
							"description"
						]
					}
				]
			},
			"NewLotSale": {
				"allOf": [
					{
						"$ref": "#/components/schemas/NewLotCommon"
					},
					{
						"type": "object",
						"properties": {
							"saleType": {
								"type": "string",
								"enum": [
									"Sale"
								],
								"description": "Тип продажи."
							},
							"bestOffer": {
								"allOf": [
									{
										"$ref": "#/components/schemas/YesOrNo"
									},
									{
										"description": "Для лотов с фиксированной ценой: Торг уместен."
									}
								]
							},
							"quantity": {
								"type": "integer",
								"nullable": true,
								"description": "Для лотов с фиксированной ценой: Количество."
							},
							"price": {
								"type": "number",
								"nullable": true,
								"description": "Для лотов с фиксированной ценой: Цена."
							}
						},
						"required": [
							"saleType",
							"quantity",
							"price"
						]
					}
				]
			},
			"NewLotAuction": {
				"allOf": [
					{
						"$ref": "#/components/schemas/NewLotCommon"
					},
					{
						"type": "object",
						"properties": {
							"saleType": {
								"type": "string",
								"enum": [
									"Auction"
								],
								"description": "Тип продажи."
							},
							"antisniper": {
								"allOf": [
									{
										"$ref": "#/components/schemas/YesOrNo"
									},
									{
										"description": "Для аукционов: Опция автопродления."
									}
								]
							},
							"notify": {
								"allOf": [
									{
										"$ref": "#/components/schemas/YesOrNo"
									},
									{
										"description": "Для аукционов: флаг оповещения о новый ставках на лот."
									}
								]
							},
							"prolong": {
								"type": "integer",
								"nullable": true,
								"description": "Для аукционов: количество продлений в случае неудачных торгов."
							},
							"startPrice": {
								"type": "number",
								"nullable": true,
								"description": "Для аукционов: начальная цена."
							},
							"strikePrice": {
								"type": "number",
								"nullable": true,
								"description": "Для аукционов: цена Купить сейчас."
							}
						},
						"required": [
							"saleType",
							"startPrice"
						]
					}
				]
			},
			"ListItemRequest": {
				"oneOf": [
					{
						"$ref": "#/components/schemas/NewLotSale"
					},
					{
						"$ref": "#/components/schemas/NewLotAuction"
					}
				],
				"discriminator": {
					"propertyName": "saleType",
					"mapping": {
						"Sale": "#/components/schemas/NewLotSale",
						"Auction": "#/components/schemas/NewLotAuction"
					}
				}
			},
			"RelistItemRequest": {
				"type": "object",
				"properties": {
					"id": {
						"type": "integer",
						"nullable": true,
						"description": "Номер лота."
					}
				},
				"required": [
					"id"
				]
			},
			"StopSaleRequest": {
				"type": "object",
				"properties": {
					"id": {
						"type": "integer",
						"nullable": true,
						"description": "Номер лота."
					}
				},
				"required": [
					"id"
				]
			},
			"UpdateLotCommon": {
				"type": "object",
				"properties": {
					"delivery": {
						"type": "string",
						"enum": [
							"NO",
							"COUNTRY",
							"WORLD"
						],
						"description": "Доставляется ли лот за пределы города:\n- `NO` - не доставляется;\n- `COUNTRY` - доставляется по стране;\n- `WORLD` - доставляется по стране и миру.",
						"example": "WORLD"
					},
					"localDelivery": {
						"type": "string",
						"enum": [
							"SELF",
							"FREE",
							"CHARGE"
						],
						"description": "Доставка по городу (одно из значений):\n- `SELF` - самовывоз;\n- `FREE` - бесплатно;\n- `CHARGE` - за плату.",
						"example": "CHARGE"
					},
					"localDeliveryPrice": {
						"type": "number",
						"nullable": true,
						"description": "Стоимость доставки по городу.\n- `0.00` - уточняйте стоимость доставки дополнительно;\n- Число больше 0 - стоимость в валюте цены.",
						"example": 50
					},
					"countryDeliveryPrice": {
						"type": "number",
						"nullable": true,
						"description": "Стоимость доставки по стране.\n- `-1` - бесплатно;\n- `0.00` - уточняйте стоимость доставки дополнительно;\n- Число больше 0 - стоимость в валюте цены.",
						"example": 100
					},
					"worldDeliveryPrice": {
						"type": "number",
						"nullable": true,
						"description": "Стоимость доставки по миру.\n- `-1` - бесплатно;\n- `0.00` - уточняйте стоимость доставки дополнительно;\n- Число больше 0 - стоимость в валюте цены.",
						"example": 150
					},
					"id": {
						"type": "integer",
						"nullable": true,
						"description": "Номер лота."
					},
					"name": {
						"type": "string",
						"description": "Наименование лота."
					},
					"category": {
						"type": "integer",
						"nullable": true,
						"description": "Номер тематического раздела"
					},
					"longevity": {
						"$ref": "#/components/schemas/Longevity"
					},
					"curencyId": {
						"type": "integer",
						"nullable": true,
						"description": "Идентификатор валюты."
					},
					"tags": {
						"type": "string",
						"description": "Метки лота через запятую."
					},
					"categoryParams": {
						"type": "string",
						"description": "Номера дополнительных параметров лота через запятую."
					},
					"bold": {
						"allOf": [
							{
								"$ref": "#/components/schemas/YesOrNo"
							},
							{
								"description": "Выделение жирным в списке лотов"
							}
						]
					},
					"recommended": {
						"allOf": [
							{
								"$ref": "#/components/schemas/YesOrNo"
							},
							{
								"description": "Промо лот"
							}
						]
					},
					"payment": {
						"$ref": "#/components/schemas/Payment"
					},
					"minimalBuyerRate": {
						"type": "integer",
						"nullable": true,
						"description": "Минимальный рейтинг покупателя. Число в диапазоне от 0 до 9."
					},
					"condition": {
						"$ref": "#/components/schemas/LotCondition"
					},
					"pictures": {
						"type": "string",
						"description": "Список URL изображений лота через запятую."
					},
					"city": {
						"type": "integer",
						"nullable": true,
						"description": "Идентификатор города, в котором расположен лот."
					},
					"deliveryText": {
						"type": "string",
						"description": "Дополнительная информация о доставке и оплате. Текст до 1000 символов."
					},
					"description": {
						"type": "string",
						"description": "Описание лота. Может быть использован HTML."
					},
					"commonDescriptions": {
						"type": "string",
						"description": "Список идентификаторов стандартных описаний для лота через запятую."
					},
					"internalId": {
						"type": "string",
						"description": "Артикул. Идентификатор лота в вашей системе."
					}
				},
				"required": [
					"id"
				]
			},
			"UpdateSaleAuction": {
				"allOf": [
					{
						"$ref": "#/components/schemas/UpdateLotCommon"
					},
					{
						"type": "object",
						"properties": {
							"saleType": {
								"type": "string",
								"enum": [
									"Sale"
								],
								"description": "Тип продажи."
							},
							"bestOffer": {
								"allOf": [
									{
										"$ref": "#/components/schemas/YesOrNo"
									},
									{
										"description": "Для лотов с фиксированной ценой: Торг уместен."
									}
								]
							},
							"quantity": {
								"type": "integer",
								"nullable": true,
								"description": "Для лотов с фиксированной ценой: Количество."
							},
							"price": {
								"type": "number",
								"nullable": true,
								"description": "Для лотов с фиксированной ценой: Цена."
							}
						}
					}
				]
			},
			"UpdateLotAuction": {
				"allOf": [
					{
						"$ref": "#/components/schemas/UpdateLotCommon"
					},
					{
						"type": "object",
						"properties": {
							"saleType": {
								"type": "string",
								"enum": [
									"Auction"
								],
								"description": "Тип продажи."
							},
							"antisniper": {
								"allOf": [
									{
										"$ref": "#/components/schemas/YesOrNo"
									},
									{
										"description": "Для аукционов: Опция автопродления."
									}
								]
							},
							"notify": {
								"allOf": [
									{
										"$ref": "#/components/schemas/YesOrNo"
									},
									{
										"description": "Для аукционов: флаг оповещения о новый ставках на лот."
									}
								]
							},
							"prolong": {
								"type": "integer",
								"nullable": true,
								"description": "Для аукционов: количество продлений в случае неудачных торгов."
							},
							"startPrice": {
								"type": "number",
								"nullable": true,
								"description": "Для аукционов: начальная цена."
							},
							"strikePrice": {
								"type": "number",
								"nullable": true,
								"description": "Для аукционов: цена Купить сейчас."
							}
						}
					}
				]
			},
			"UpdateItemRequest": {
				"anyOf": [
					{
						"$ref": "#/components/schemas/UpdateSaleAuction"
					},
					{
						"$ref": "#/components/schemas/UpdateLotAuction"
					}
				]
			}
		},
		"parameters": {}
	},
	"paths": {
		"/getAccountInfo": {
			"post": {
				"summary": "Получить информацию об аккаунте",
				"description": "Стоимость выполнения запроса: 1 очко.",
				"tags": [
					"Аккаунт"
				],
				"requestBody": {
					"content": {
						"application/json": {
							"schema": {
								"type": "object",
								"properties": {}
							},
							"example": {}
						},
						"application/x-www-form-urlencoded": {
							"schema": {
								"type": "object",
								"properties": {}
							},
							"example": {}
						}
					}
				},
				"responses": {
					"200": {
						"description": "Success",
						"content": {
							"application/json": {
								"schema": {
									"$ref": "#/components/schemas/GetAccountInfoResponse"
								}
							}
						}
					}
				}
			}
		},
		"/getCategoryInfo": {
			"post": {
				"summary": "Получить информацию о тематическом разделе",
				"description": "Стоимость выполнения запроса: 1 очко.",
				"tags": [
					"Справочники"
				],
				"requestBody": {
					"content": {
						"application/json": {
							"schema": {
								"$ref": "#/components/schemas/GetCategoryInfoRequest"
							},
							"example": {
								"id": 1798
							}
						},
						"application/x-www-form-urlencoded": {
							"schema": {
								"$ref": "#/components/schemas/GetCategoryInfoRequest"
							},
							"example": {
								"id": 1798
							}
						}
					}
				},
				"responses": {
					"200": {
						"description": "Success",
						"content": {
							"application/json": {
								"schema": {
									"$ref": "#/components/schemas/GetCategoryInfoResponse"
								}
							}
						}
					}
				}
			}
		},
		"/getCitiesList": {
			"post": {
				"summary": "Получить список городов в стране",
				"description": "Стоимость выполнения запроса: 1 очко.",
				"tags": [
					"Справочники"
				],
				"requestBody": {
					"content": {
						"application/json": {
							"schema": {
								"$ref": "#/components/schemas/GetCitiesListRequest"
							},
							"example": {
								"id": 107
							}
						},
						"application/x-www-form-urlencoded": {
							"schema": {
								"$ref": "#/components/schemas/GetCitiesListRequest"
							},
							"example": {
								"id": 107
							}
						}
					}
				},
				"responses": {
					"200": {
						"description": "Success",
						"content": {
							"application/json": {
								"schema": {
									"$ref": "#/components/schemas/GetCitiesListResponse"
								}
							}
						}
					}
				}
			}
		},
		"/getCountryList": {
			"post": {
				"summary": "Получить список стран",
				"description": "Стоимость выполнения запроса: 1 очко.",
				"tags": [
					"Справочники"
				],
				"requestBody": {
					"content": {
						"application/json": {
							"schema": {
								"type": "object",
								"properties": {}
							}
						},
						"application/x-www-form-urlencoded": {
							"schema": {
								"type": "object",
								"properties": {}
							}
						}
					}
				},
				"responses": {
					"200": {
						"description": "Success",
						"content": {
							"application/json": {
								"schema": {
									"$ref": "#/components/schemas/GetCountryListResponse"
								}
							}
						}
					}
				}
			}
		},
		"/getCurencyList": {
			"post": {
				"summary": "Получить список валют",
				"description": "Стоимость выполнения запроса: 1 очко.",
				"tags": [
					"Справочники"
				],
				"requestBody": {
					"content": {
						"application/json": {
							"schema": {
								"type": "object",
								"properties": {}
							}
						},
						"application/x-www-form-urlencoded": {
							"schema": {
								"type": "object",
								"properties": {}
							}
						}
					}
				},
				"responses": {
					"200": {
						"description": "Success",
						"content": {
							"application/json": {
								"schema": {
									"$ref": "#/components/schemas/GetCurrencyListResponse"
								}
							}
						}
					}
				}
			}
		},
		"/getSubCategory": {
			"post": {
				"summary": "Получить список подразделов",
				"description": "Стоимость выполнения запроса: 1 очко.\n\nМетод возвращает список подразделов для указанной категории.\n      В ответе возвращается список подразделов, для которых нет подкатегорий.\n      Для каждого подраздела возвращается его идентификатор, название, признак того,\n      что у подраздела есть подкатегории, и рекомендуемая цена.",
				"tags": [
					"Справочники"
				],
				"requestBody": {
					"content": {
						"application/json": {
							"schema": {
								"$ref": "#/components/schemas/GetSubCategoryRequest"
							},
							"example": {
								"id": 126
							}
						},
						"application/x-www-form-urlencoded": {
							"schema": {
								"$ref": "#/components/schemas/GetSubCategoryRequest"
							},
							"example": {
								"id": 126
							}
						}
					}
				},
				"responses": {
					"200": {
						"description": "Success",
						"content": {
							"application/json": {
								"schema": {
									"$ref": "#/components/schemas/GetSubCategoryResponse"
								}
							}
						}
					}
				}
			}
		},
		"/deleteItem": {
			"post": {
				"summary": "Удалить лот из списка завершенных",
				"description": "Стоимость выполнения запроса: 0 очков.",
				"tags": [
					"Лоты"
				],
				"requestBody": {
					"content": {
						"application/json": {
							"schema": {
								"$ref": "#/components/schemas/DeleteItemRequest"
							},
							"example": {
								"id": 123456789
							}
						},
						"application/x-www-form-urlencoded": {
							"schema": {
								"$ref": "#/components/schemas/DeleteItemRequest"
							},
							"example": {
								"id": 123456789
							}
						}
					}
				},
				"responses": {
					"200": {
						"description": "Success",
						"content": {
							"application/json": {
								"schema": {
									"$ref": "#/components/schemas/DeleteItemResponse"
								}
							}
						}
					}
				}
			}
		},
		"/getCommonDescriptionList": {
			"post": {
				"summary": "Получить список стандартных описаний",
				"description": "Стоимость выполнения запроса: 1 очко.",
				"tags": [
					"Лоты"
				],
				"requestBody": {
					"content": {
						"application/json": {
							"schema": {
								"$ref": "#/components/schemas/GetCommonDescriptionListRequest"
							}
						},
						"application/x-www-form-urlencoded": {
							"schema": {
								"$ref": "#/components/schemas/GetCommonDescriptionListRequest"
							}
						}
					}
				},
				"responses": {
					"200": {
						"description": "Success",
						"content": {
							"application/json": {
								"schema": {
									"$ref": "#/components/schemas/GetCommonDescriptionListResponse"
								}
							}
						}
					}
				}
			}
		},
		"/getFinishedItemList": {
			"post": {
				"summary": "Получить список лотов, торги по которым завершены",
				"description": "Стоимость выполнения запроса: 10 очков.",
				"tags": [
					"Лоты"
				],
				"requestBody": {
					"content": {
						"application/json": {
							"schema": {
								"$ref": "#/components/schemas/GetFinishedItemListRequest"
							}
						},
						"application/x-www-form-urlencoded": {
							"schema": {
								"$ref": "#/components/schemas/GetFinishedItemListRequest"
							}
						}
					}
				},
				"responses": {
					"200": {
						"description": "Success",
						"content": {
							"application/json": {
								"schema": {
									"$ref": "#/components/schemas/GetFinishedItemListResponse"
								}
							}
						}
					}
				}
			}
		},
		"/getItemInfo": {
			"post": {
				"summary": "Получить информацию о лоте",
				"description": "Стоимость выполнения запроса: 1 очко.",
				"tags": [
					"Лоты"
				],
				"requestBody": {
					"content": {
						"application/json": {
							"schema": {
								"$ref": "#/components/schemas/GetItemInfoRequest"
							}
						},
						"application/x-www-form-urlencoded": {
							"schema": {
								"$ref": "#/components/schemas/GetItemInfoRequest"
							}
						}
					}
				},
				"responses": {
					"200": {
						"description": "Success",
						"content": {
							"application/json": {
								"schema": {
									"$ref": "#/components/schemas/GetItemInfoResponse"
								}
							}
						}
					}
				}
			}
		},
		"/getItemList": {
			"post": {
				"summary": "Получить список лотов, находящихся на продаже",
				"description": "Стоимость выполнения запроса: 10 очков.",
				"tags": [
					"Лоты"
				],
				"requestBody": {
					"content": {
						"application/json": {
							"schema": {
								"$ref": "#/components/schemas/GetItemListRequest"
							}
						},
						"application/x-www-form-urlencoded": {
							"schema": {
								"$ref": "#/components/schemas/GetItemListRequest"
							}
						}
					}
				},
				"responses": {
					"200": {
						"description": "Success",
						"content": {
							"application/json": {
								"schema": {
									"$ref": "#/components/schemas/GetItemListResponse"
								}
							}
						}
					}
				}
			}
		},
		"/getSoldFinishedItemList": {
			"post": {
				"summary": "Получить список лотов, торги по которым завершены и по ним есть сделки",
				"description": "Стоимость выполнения запроса: 10 очков.",
				"tags": [
					"Лоты"
				],
				"requestBody": {
					"content": {
						"application/json": {
							"schema": {
								"$ref": "#/components/schemas/GetSoldFinishedItemListRequest"
							}
						},
						"application/x-www-form-urlencoded": {
							"schema": {
								"$ref": "#/components/schemas/GetSoldFinishedItemListRequest"
							}
						}
					}
				},
				"responses": {
					"200": {
						"description": "Success",
						"content": {
							"application/json": {
								"schema": {
									"$ref": "#/components/schemas/GetSoldFinishedItemListResponse"
								}
							}
						}
					}
				}
			}
		},
		"/getUnsoldFinishedItemList": {
			"post": {
				"summary": "Получить список лотов, торги по которым завершены и по ним нет ни одной сделки",
				"description": "Стоимость выполнения запроса: 10 очков.",
				"tags": [
					"Лоты"
				],
				"requestBody": {
					"content": {
						"application/json": {
							"schema": {
								"$ref": "#/components/schemas/GetUnsoldFinishedItemListRequest"
							}
						},
						"application/x-www-form-urlencoded": {
							"schema": {
								"$ref": "#/components/schemas/GetUnsoldFinishedItemListRequest"
							}
						}
					}
				},
				"responses": {
					"200": {
						"description": "Success",
						"content": {
							"application/json": {
								"schema": {
									"$ref": "#/components/schemas/GetUnsoldFinishedItemListResponse"
								}
							}
						}
					}
				}
			}
		},
		"/listItem": {
			"post": {
				"summary": "Выставить лот на продажу",
				"description": "Стоимость выполнения запроса: 10 очков.",
				"tags": [
					"Лоты"
				],
				"requestBody": {
					"content": {
						"application/json": {
							"schema": {
								"$ref": "#/components/schemas/ListItemRequest"
							}
						},
						"application/x-www-form-urlencoded": {
							"schema": {
								"$ref": "#/components/schemas/ListItemRequest"
							}
						}
					}
				},
				"responses": {
					"200": {
						"description": "Success",
						"content": {
							"application/json": {
								"schema": {
									"$ref": "#/components/schemas/ListItemResponse"
								}
							}
						}
					}
				}
			}
		},
		"/relistItem": {
			"post": {
				"summary": "Перевыставить лот на повторные торги",
				"description": "Стоимость выполнения запроса: 2 очка.",
				"tags": [
					"Лоты"
				],
				"requestBody": {
					"content": {
						"application/json": {
							"schema": {
								"$ref": "#/components/schemas/RelistItemRequest"
							},
							"example": {
								"id": 290730300
							}
						},
						"application/x-www-form-urlencoded": {
							"schema": {
								"$ref": "#/components/schemas/RelistItemRequest"
							},
							"example": {
								"id": 290730300
							}
						}
					}
				},
				"responses": {
					"200": {
						"description": "Success",
						"content": {
							"application/json": {
								"schema": {
									"$ref": "#/components/schemas/RelistItemResponse"
								}
							}
						}
					}
				}
			}
		},
		"/stopSale": {
			"post": {
				"summary": "Снять лот с продажи",
				"description": "Стоимость выполнения запроса: 0 очков.",
				"tags": [
					"Лоты"
				],
				"requestBody": {
					"content": {
						"application/json": {
							"schema": {
								"$ref": "#/components/schemas/StopSaleRequest"
							},
							"example": {
								"id": 290730300
							}
						},
						"application/x-www-form-urlencoded": {
							"schema": {
								"$ref": "#/components/schemas/StopSaleRequest"
							},
							"example": {
								"id": 290730300
							}
						}
					}
				},
				"responses": {
					"200": {
						"description": "Success",
						"content": {
							"application/json": {
								"schema": {
									"$ref": "#/components/schemas/StopSaleResponse"
								}
							}
						}
					}
				}
			}
		},
		"/updateItem": {
			"post": {
				"summary": "Изменить параметры существующего лота",
				"description": "Стоимость выполнения запроса: 1 очко.",
				"tags": [
					"Лоты"
				],
				"requestBody": {
					"content": {
						"application/json": {
							"schema": {
								"$ref": "#/components/schemas/UpdateItemRequest"
							}
						},
						"application/x-www-form-urlencoded": {
							"schema": {
								"$ref": "#/components/schemas/UpdateItemRequest"
							}
						}
					}
				},
				"responses": {
					"200": {
						"description": "Success",
						"content": {
							"application/json": {
								"schema": {
									"$ref": "#/components/schemas/UpdateItemResponse"
								}
							}
						}
					}
				}
			}
		}
	}
}