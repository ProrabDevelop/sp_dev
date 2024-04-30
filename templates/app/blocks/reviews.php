<?php
$reply = $reply ?? false;

/* @var $reviews \app\std_models\review[] */
foreach ($reviews as $review): ?>
    <div class="review">
        <div class="avatar" style="background-image: url('<?= get_avatar($review->user_id, URL.'assets/img/service-no-photo.svg') ?>')"></div>
        <div class="review-content">
            <div class="review-header">
                <div class="avatar" style="background-image: url('<?= get_avatar($review->user_id, URL.'assets/img/service-no-photo.svg') ?>')"></div>
                <div class="author">
                    <div class="name"> <?= $review->reviewer_name; ?></div>
                    <div class="rating">
                        <?php $review->render_stars(); ?>
                        <span><?= $review->score ?></span>
                    </div>
                </div>
                <div class="time">
                    <?= human_date($review->time); ?>
                </div>
            </div>
            <div class="review-text">
                <?php
                $reviewContent = htmlspecialchars($review->content);
                $cropedReviewContent = crop_text(REVIEW_WORD_COUNT, $reviewContent);
                $croped = strlen($cropedReviewContent) < strlen($reviewContent);
                ?>
                <div class="review-text-croped">
                    <?php if ($croped): ?>
                        <?= $cropedReviewContent ?>...
                    <?php else: ?>
                        <?= $reviewContent ?>
                    <?php endif; ?>
                </div>
                <?php if ($croped): ?>
                    <div class="review-text-full">
                        <?= $reviewContent ?>
                    </div>
                <?php endif; ?>

                <?php if ($croped): ?>
                    <div class="expand-link down">
                        <span class="up">Скрыть</span>
                        <span class="down">Показать полностью</span>
                        <img src="<?= URL.'assets/img/icons/arrow-down.svg' ?>" alt="" width="24" height="24" />
                    </div>
                <?php endif; ?>

                <?php if ($review->answer): ?>
                    <div class="review-answer">
                        <div class="answer-header">
                            <span class="label">Ответ мастера</span>
                            <span class="time">
                                <?php // human_date() //TODO need implementation ?>
                            </span>
                        </div>
                        <div class="answer-content">
                            <?= $review->answer; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <?php if ($reply): ?>
                <div class="review-reply-row">
                    <?php if (!$review->answer): ?>
                        <a class="review-reply text-button" review_id="<?= $review->id?>">
                            <i class="icon icon-tg"></i>
                            <span>Ответить</span>
                        </a>
                    <?php endif; ?>
                    <a class="review-complain text-button" review_id="<?= $review->id?>">
                        <i class="icon icon-warning"></i>
                        <span>Пожаловаться</span>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endforeach; ?>
