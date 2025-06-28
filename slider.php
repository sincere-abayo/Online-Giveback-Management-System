<style>
.image-slider {
  overflow: hidden;
  position: relative;
  margin: 4rem 0;
  border-radius: 20px;
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
  background: #f8f9fa;
}

.slider-inner {
  display: flex;
  white-space: nowrap;
  animation: slide 25s linear infinite;
  height: 300px;
}

.slider-inner img {
  width: 300px;
  height: 300px;
  object-fit: cover;
  margin: 0 15px;
  border-radius: 15px;
  transition: transform 0.3s ease;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.slider-inner img:hover {
  transform: scale(1.05);
}

@keyframes slide {
  from { transform: translateX(0); }
  to { transform: translateX(-100%); }
}

.slider-inner:hover {
  animation-play-state: paused;
}

/* Add gradient overlay on sides for smooth fade effect */
.image-slider::before,
.image-slider::after {
  content: '';
  position: absolute;
  top: 0;
  width: 100px;
  height: 100%;
  z-index: 2;
  pointer-events: none;
}

.image-slider::before {
  left: 0;
  background: linear-gradient(to right, #f8f9fa, transparent);
}

.image-slider::after {
  right: 0;
  background: linear-gradient(to left, #f8f9fa, transparent);
}
</style>
<div class="image-slider">
  <div class="slider-inner">
    <img src="slide/0.png" alt="1">
    <img src="slide/1.png" alt="1">
    <img src="slide/2.png" alt="2">
    <img src="slide/3.png" alt="3">
    <img src="slide/4.png" alt="4">
    <img src="slide/5.png" alt="5">
    <img src="slide/6.png" alt="6">
    <img src="slide/7.png" alt="7">
    <img src="slide/8.png" alt="8">
    <img src="slide/9.png" alt="8">
    <img src="slide/10.png" alt="8">
    </div>
</div>
