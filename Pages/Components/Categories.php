<?php
// Fetch ONLY parent categories that are set to display
$parentQuery = "SELECT * FROM categories WHERE parent_id = 0 AND display = 1";
$parentResult = $conn->query($parentQuery);

$parentCategories = [];
while ($parent = $parentResult->fetch_assoc()) {
    // Fetch ALL child categories regardless of their display status
    $childQuery = "SELECT * FROM categories WHERE parent_id = ".$parent['category_id'];
    $childResult = $conn->query($childQuery);
    
    $children = [];
    while ($child = $childResult->fetch_assoc()) {
        $children[] = $child;
    }
    
    $parent['children'] = $children;
    $parentCategories[] = $parent;
}
?>

<!-- Dynamic carousel generation -->
<!-- Dynamic carousel generation -->
<?php foreach ($parentCategories as $index => $parent): ?>
    <div class="category-1"> <!-- Changed from category-<?php echo $index + 1; ?> -->
        <div class="container-fluid px-5">
            <h1 class="text-center mt-5 mb-4 heading-main">Categories of <?php echo htmlspecialchars($parent['name']); ?></h1>

            <div class="slider-wrapper">
                <button id="prev-slide" class="slide-button material-symbols-rounded">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <ul class="image-list">
                    <?php foreach ($parent['children'] as $child): ?>
                        <li class="image-item-container">
                            <img class="image-item" src="./Assets/uploads/categories/<?php echo htmlspecialchars($child['image']); ?>" alt="<?php echo htmlspecialchars($child['name']); ?>" />
                            <div class="image-item-text"><?php echo htmlspecialchars($child['name']); ?></div>
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
        </div>
    </div>
    
    <!-- JavaScript for this carousel -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const initSlider = () => {
                const imageList = document.querySelector(".category-1 .slider-wrapper .image-list");
                const slideButtons = document.querySelectorAll(".category-1 .slider-wrapper .slide-button");
                const sliderScrollbar = document.querySelector(".category-1 .slider-scrollbar");
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
                    }

                    const handleMouseUp = () => {
                        document.removeEventListener("mousemove", handleMouseMove);
                        document.removeEventListener("mouseup", handleMouseUp);
                    }

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
                }

                const updateScrollThumbPosition = () => {
                    const scrollPosition = imageList.scrollLeft;
                    const thumbPosition = (scrollPosition / maxScrollLeft) * (sliderScrollbar.clientWidth - scrollbarThumb.offsetWidth);
                    scrollbarThumb.style.left = `${thumbPosition}px`;
                }

                imageList.addEventListener("scroll", () => {
                    updateScrollThumbPosition();
                    handleSlideButtons();
                });
            }

            window.addEventListener("resize", initSlider);
            initSlider();
        });
    </script>
<?php endforeach; ?>