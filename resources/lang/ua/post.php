<?php

return array(
  'publishedPostsLimitReached' => 'limit::posts',
  'tagDoesNotExist' => 'категорія :value не існує',
  'attributeDoesNotExist' => 'атрибут " :param1" не існує',
  'tooManyValues' => 'Забагато значень для :param1 (max: :param2)',
  'valueDoesNotExist' => 'ідентифікатор значення :param2 для поля :param1 не існує',
  'canNotUseExistingValueOnNewPost' => 'Не вдається використати існуюче значення :param2 для поля :param1 для нового повідомлення',
  'postAttributeRequired' => 'атрибут :необхідний param1',
  'taskAttributeRequired' => 'атрибут :param1 необхідний перед етапом " :param2" можна завершити',
  'emptyIdAndLocale' => 'Повинен мати принаймні ідентифікатор або локаль',
  'emptyParentWithLocale' => 'Потрібно мати батьківський ідентифікатор при передачі локалі',
  'notAnArray' => 'Значення повідомлення для :param1 повинен бути масивом',
  'scalar' => 'Значення повідомлення для :param1 повинен бути скалярним',
  'doesTranslationExist' => 'Переклад :value для повідомлення :param2 вже існує',
  'isSlugAvailable' => ' :поле :value вже використовується',
  'published_to' => масив (
    'Існує' => 'Роль, яку ви публікуєте в " :value" не існує'
  ),
  'stageDoesNotExist' => 'Етап " :param1" не існує',
  'stageRequired' => 'Етап " :перед публікацією потрібно param1",
  'postNeedsApprovalBeforePublishing' => "Публікація потребує схвалення адміністратором, перш ніж її можна буде опублікувати",
  'postCanOnlyBeUnpublishedByAdmin' => "Публікацію може опублікувати лише адміністратор",
  'alreadyLockedByDifferentUser' => "Наразі публікація заблокована іншим користувачем і не може бути оновлена.",
  'values' => масив (
      'date' => 'Поле :param1 має бути датою, враховуючи: :param2',
      'decimal' => 'Поле :param1 має бути десятковим з двома місцями, Дано: :param2',
      'digit' => 'Поле :param1 повинно бути розрядом, Дано: :param2',
      'email' => 'Поле :param1 має бути адресою електронної пошти, враховуючи: :param2',
      'Існує' => 'Поле :param1 має бути дійсним ідентифікатором повідомлення, Ідентифікатор повідомлення: :param2',
      'tagExists' => 'Поле :param1 має бути дійсним ідентифікатором категорії або ім'ям, Категорія: :param2',
      'max_length' => 'Поле :param1 не повинно перевищувати :param2 символів завдовжки, враховуючи: :param2',
      'invalidForm' => 'Поле :param1 має неправильний тип публікації, ідентифікатор повідомлення: :param2',
      'numeric' => 'Поле :param1 має бути числовим, Дано: :param2',
      'scalar' => 'Поле :param1 має бути скалярним, враховуючи: :param2',
      'point' => 'Поле :param1 має бути масивом lat і lon',
      'lat' => 'поле :param1 має містити дійсну широту',
      'lon' => 'поле :param1 має містити дійсну довготу',
      'url' => 'Поле :param1 має бути URL-адресою, враховуючи: :param2',
      'video_type' => 'Поле :param1 має бути або ютубом, або URL-адресою vimeo, враховуючи: :param2',
  )
);
