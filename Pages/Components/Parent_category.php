<?php
$base_url = '/Project'; // Adjust as needed or use $_SERVER['HTTP_HOST'] for dynamic detection
$show_heading = isset($show_heading) ? $show_heading : true; // Default to true if not set

// Fetch parent categories that have products (directly or via subcategories)
$parentQuery = "
    SELECT DISTINCT c.*
    FROM categories c
    LEFT JOIN product_categories pc ON c.category_id = pc.category_id
    LEFT JOIN categories sub ON sub.parent_id = c.category_id
    LEFT JOIN product_categories sub_pc ON sub.category_id = sub_pc.category_id
    WHERE c.parent_id = 0 AND c.display = 1
    AND (pc.product_id IS NOT NULL OR sub_pc.product_id IS NOT NULL)
    ORDER BY c.name
";
$parentResult = $conn->query($parentQuery);
$parentCategories = [];
if ($parentResult) {
    while ($parent = $parentResult->fetch_assoc()) {
        $parentCategories[] = $parent;
    }
}
?>

<style>
/* Assuming styles are in ../Assets/css/style.css or elsewhere */
/* Minimal CSS for clickable items to ensure consistent appearance */
.image-item-container a {
    text-decoration: none; /* Remove underline from links */
    color: inherit; /* Inherit text color for image-item-text */
    display: block; /* Make the entire item clickable */
    width: 100%;
    height: 100%;
}
.image-item-container a:hover {
    opacity: 0.9; /* Subtle hover effect to indicate clickability */
}
</style>

<!-- Single Carousel for Parent Categories -->
<div class="category-list category-main">
    <div class="container-fluid px-5">
        <?php if ($show_heading): ?>
            <h1 class="text-center mt-5 mb-4 heading-main">Main Categories</h1>
        <?php endif; ?>

        <?php if (!empty($parentCategories)): ?>
            <div class="slider-wrapper">
                <button id="prev-slide" class="slide-button material-symbols-rounded">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <ul class="image-list">
                    <?php foreach ($parentCategories as $parent): ?>
                        <li class="image-item-container">
                            <a href="<?php echo $base_url . '/Pages/Categorized_products.php?category_id=' . (int)$parent['category_id']; ?>">
                                <img class="image-item"
                                     src="<?php echo $base_url . '/Assets/uploads/categories/' . htmlspecialchars($parent['image']); ?>"
                                     alt="<?php echo htmlspecialchars($parent['name']); ?>" />
                                <div class="image-item-text"><?php echo htmlspecialchars($parent['name']); ?></div>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <button id="next-slide" class="slide-button material-symbols-rounded">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            <div class="slider-scrollbar">
                <div class="scrollbar-track">
                    <div class="scrollbar-thumb"></div>
                </div>
            </div>
        <?php else: ?>
            <p class="text-center text-muted mt-4">No main categories available.</p>
        <?php endif; ?>
    </div>
</div>

<!-- JavaScript for the Carousel -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const initSlider = () => {
        const imageList = document.querySelector(".category-main .slider-wrapper .image-list");
        const slideButtons = document.querySelectorAll(".category-main .slider-wrapper .slide-button");
        const sliderScrollbar = document.querySelector(".category-main .slider-scrollbar");
        const scrollbarThumb = sliderScrollbar.querySelector(".scrollbar-thumb");
        const maxScrollLeft = imageList.scrollWidth - imageList.clientWidth;

        // Handle scrollbar thumb drag
        scrollbarThumb.addEventListener("mousedown", (e) => {
            const startX = e.clientX;
            const thumbPosition = scrollbarThumb.offsetLeft;
            const maxThumbPosition = sliderScrollbar.getBoundingClientRect().width - scrollbarThumb.offsetWidth;

            const handleMouseMove = (e) => {
                const deltaX = e.clientX - startX;
                const newThumbPosition = thumbPosition + deltaX;
                const boundedPosition = Math.max(0, Math.min(maxThumbPosition, newThumbPosition));
                const scrollPosition = (boundedPosition / maxThumbPosition) * maxScrollLeft;

                scrollbarThumb.style.left = `${boundedPosition}px`;
                imageList.scrollLeft = scrollPosition;
            };

            const handleMouseUp = () => {
                document.removeEventListener("mousemove", handleMouseMove);
                document.removeEventListener("mouseup", handleMouseUp);
            };

            document.addEventListener("mousemove", handleMouseMove);
            document.addEventListener("mouseup", handleMouseUp);
        });

        // Slide images according to the slide button clicks
        slideButtons.forEach(button => {
            button.addEventListener("click", () => {
                const direction = button.id === "prev-slide" ? -1 : 1;
                const scrollAmount = imageList.clientWidth * direction;
                imageList.scrollBy({ left: scrollAmount, behavior: "smooth" });
            });
        });

        const handleSlideButtons = () => {
            slideButtons[0].style.display = imageList.scrollLeft <= 0 ? "none" : "flex";
            slideButtons[1].style.display = imageList.scrollLeft >= maxScrollLeft ? "none" : "flex";
        };

        const updateScrollThumbPosition = () => {
            const scrollPosition = imageList.scrollLeft;
            const thumbPosition = (scrollPosition / maxScrollLeft) * (sliderScrollbar.clientWidth - scrollbarThumb.offsetWidth);
            scrollbarThumb.style.left = `${thumbPosition}px`;
        };

        imageList.addEventListener("scroll", () => {
            updateScrollThumbPosition();
            handleSlideButtons();
        });

        // Initialize
        handleSlideButtons();
        updateScrollThumbPosition();
    };

    window.addEventListener("resize", initSlider);
    initSlider();
});
</script>