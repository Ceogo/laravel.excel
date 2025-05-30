## Было составлено специально для Илюши


### Документация контроллера

Ниже представлена подробная документация для `ScheduleController` с визуальным объяснением каждой функции.

#### Метод `generateSchedule`
- **Назначение**: Основной метод, который генерирует расписание для группы на указанную неделю и семестр.
- **Параметры**:
  - `$group`: Объект группы (`Group`).
  - `$semester`: Номер семестра (int).
  - `$week`: Номер недели (int).
  - `$bellSchedule`: Массив расписания звонков (нахуй не всрался).
- **Возвращает**: Массив расписания, структурированный по дням недели.
- **Визуальное объяснение**:
  1. **Инициализация**: Создается пустой массив расписания с ключами для дней недели (понедельник–пятница).
  2. **Получение данных**: Собираются обычные пары и пары из `LessonLine`.
  3. **Распределение**: Общее количество пар делится между днями недели.
  4. **Назначение**: Пары распределяются по дням с учетом доступных кабинетов.
  5. **Обновление**: Статусы `LessonLine` сбрасываются для следующей недели.

#### Метод `ensureLessonLinesForGroup`
- **Назначение**: Добавляет пары с нагрузкой 1 час в неделю в таблицу `LessonLine`, если их там еще нет.
- **Параметры**:
  - `$group`: Объект группы.
  - `$semester`: Номер семестра.
- **Визуальное объяснение**:
  - **Шаг 1**: Выборка всех `LearningOutcome` с `hours_per_week = 1`.
  - **Шаг 2**: Проверка, есть ли запись в `LessonLine`.
  - **Шаг 3**: Если записи нет, создается новая с `is_processed = false`.
  - *Пример*: Если у группы есть предмет "Физика" с 1 часом в неделю, он добавляется в `LessonLine`.

#### Метод `getRegularLessons`
- **Назначение**: Возвращает обычные еженедельные пары, которые не находятся в `LessonLine`.
- **Параметры**:
  - `$group`: Объект группы.
  - `$semester`: Номер семестра.
- **Возвращает**: Коллекцию объектов `LearningOutcome`.
- **Визуальное объяснение**:
  - **Фильтрация**: Исключаются все ID из `LessonLine`.
  - **Результат**: Остаются только пары, которые проводятся регулярно (например, 2+ часов в неделю).

#### Метод `getLessonDetails`
- **Назначение**: Получает информацию об учебном результате по его ID.
- **Параметры**:
  - `$learningOutcomeId`: ID учебного результата.
- **Возвращает**: Объект `LearningOutcome`.
- **Визуальное объяснение**:
  - Простой поиск в базе данных с обработкой ошибки, если запись не найдена.

#### Метод `distributePairs`
- **Назначение**: Равномерно распределяет пары по дням недели.
- **Параметры**:
  - `$totalPairs`: Общее количество пар.
  - `$daysCount`: Количество дней (обычно 5).
- **Возвращает**: Массив с количеством пар для каждого дня.
- **Визуальное объяснение**:
  - **Пример**: 10 пар, 5 дней → базово 2 пары на день.
  - **Остаток**: Если есть остаток (10 % 5 = 0), он распределяется по первым дням.

#### Метод `getAvailableCabinet`
- **Назначение**: Находит свободный кабинет для пары в заданное время.
- **Параметры**:
  - `$day`: День недели (строка).
  - `$pairNumber`: Номер пары (int).
  - `$week`: Номер недели (int).
  - `$semester`: Номер семестра (int).
- **Возвращает**: Объект `Cabinet` или `null`.
- **Визуальное объяснение**:
  - **Проверка**: Какие кабинеты заняты в это время?
  - **Выбор**: Первый свободный кабинет из оставшихся.

#### Метод `updateLessonLineStatuses`
- **Назначение**: Сбрасывает статус `is_processed` для подготовки к следующей неделе.
- **Параметры**:
  - `$group`: Объект группы.
- **Визуальное объяснение**:
  - **Цикл**: Проходит по всем записям `LessonLine` группы.
  - **Сброс**: Если `is_processed = true`, меняет на `false`.

#### Метод `generate`
- **Назначение**: Публичный метод для обработки HTTP-запроса и вызова генерации расписания.
- **Параметры**:
  - `$request`: Объект запроса с `group_id`, `semester`, `week`.
- **Возвращает**: JSON-ответ с расписанием.
- **Визуальное объяснение**:
  - **Вход**: Получает данные из запроса.
  - **Вызов**: Запускает `generateSchedule`.
  - **Выход**: Возвращает расписание в формате JSON.

### Примечания
- Убедитесь, что все модели (`Group`, `LearningOutcome`, `LessonLine`, `Cabinet`, `Schedule`) настроены и имеют нужные поля.
- Расписание генерируется через метод `generate`
