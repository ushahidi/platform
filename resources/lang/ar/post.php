<?php

return array(
  'publishedPostsLimitReached' => 'limit::posts',
  'tagDoesNotExist' => 'category :value does not exist',
  'attributeDoesNotExist' => 'attribute ":param1" does not exist',
  'tooManyValues' => 'Too many values for :param1 (max: :param2)',
  'valueDoesNotExist' => 'value id :param2 for field :param1 does not exist',
  'canNotUseExistingValueOnNewPost' => 'Cannot use existing value :param2 for field :param1 on a new post',
  'postAttributeRequired' => 'attribute :param1 is required',
  'taskAttributeRequired' => 'attribute :param1 is required before stage ":param2" can be completed',
  'emptyIdAndLocale' => 'Must have at least id or locale',
  'emptyParentWithLocale' => 'Must have at parent id when passing locale',
  'notAnArray' => 'Post values for :param1 must be an array',
  'scalar' => 'Post values for :param1 must be scalar',
  'doesTranslationExist' => 'Translation :value for post :param2 already exists',
  'isSlugAvailable' => ':field :value is already in use',
  'published_to' => array(
    'exists' => 'الدور الذي تطبقه على ":value" غير موجود'
  ),
  'stageDoesNotExist' => 'Stage ":param1" does not exist',
  'stageRequired' => 'Stage ":param1" is required before publishing',
  'postNeedsApprovalBeforePublishing' => "Post needs approval by an administrator before it can be published",
  'postCanOnlyBeUnpublishedByAdmin' => "Post can only be unpublished by an administrator",
  'alreadyLockedByDifferentUser' => "Post is currently locked by a different user and can not be updated.",
  'values' => array(
      'date'          => 'يجب أن يكون الحقل :param1 تاريخاً، Given: :param2',
      'decimal'       => 'يجب أن يكون الحقل :param1 عدداً عُشرياً بخانتين، Given: :param2',
      'digit'         => 'يجب أن يكون الحقل :param1 رقماً، Given: :param2',
      'email'         => 'يجب أن يكون الحقل :param1 عنوان بريد إلكتروني، Given: :param2',
      'exists'        => 'الدور الذي تطبقه على ":value" غير موجود',
      'tagExists'     => 'يجب أن يكون الحقل :param1 معرّف أو اسم فئة صالح، Category: :param2',
      'max_length'    => 'يجب ألا تتجاوز حروف الحقل :param1 عدد :param2، Given: :param2',
      'invalidForm'   => 'يحوي الحقل :param1 على نوع المنشور الخاطئ، Post id: :param2',
      'numeric'       => 'يجب أن يكون الحقل :param1  عددياً، Given: :param2',
      'scalar'        => 'يجب أن يكون الحقل :param1 عدداً سُلمياً، Given: :param2',
      'point'         => 'يجب أن يكون الحقل :param1 مصفوفة خطوط طول وخطوط عرض',
      'lat'           => 'يجب أن يحوي الحقل :param1 قيمة خط عرض صالحة',
      'lon'           => 'يجب أن يحوي الحقل :param1 قيمة خط طول صالحة',
      'url'           => 'يجب أن يكون الحقل :param1 رابطاً، Given: :param2',
      'video_type'    => 'يجب أن يكون الحقل :param1 رابطاً من يوتيوب أو فيميو، Given: :param2',
  )
);
