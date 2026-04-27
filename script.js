/* ─────────────────────────────────────────────
   GLOBAL / SHARED FUNCTIONS
───────────────────────────────────────────── */

function getActiveHamburger() {
  return document.getElementById("hamburger") || document.getElementById("bd-peer-hamburger");
}

/* Drawer functions used by home page + peer profile */
function toggleDrawer() {
  const drawer = document.getElementById("drawer");
  const isOpen = drawer && drawer.classList.contains("open");

  if (isOpen) {
    closeDrawer();
  } else {
    openDrawer();
  }
}

function openDrawer() {
  const drawer = document.getElementById("drawer");
  const overlay = document.getElementById("overlay");
  const hamburger = getActiveHamburger();

  if (drawer) drawer.classList.add("open");
  if (overlay) overlay.classList.add("show");
  if (hamburger) hamburger.classList.add("active");
}

function closeDrawer() {
  const drawer = document.getElementById("drawer");
  const overlay = document.getElementById("overlay");
  const hamburger = getActiveHamburger();

  if (drawer) drawer.classList.remove("open");
  if (overlay) overlay.classList.remove("show");
  if (hamburger) hamburger.classList.remove("active");
}


/* ─────────────────────────────────────────────
   APP INIT
───────────────────────────────────────────── */

document.addEventListener("DOMContentLoaded", function () {
  initializeHomePage();
  initializePeerProfilePage();
});


/* ─────────────────────────────────────────────
   HOME PAGE
───────────────────────────────────────────── */

function initializeHomePage() {
  /* This checks whether we are inside the home page */
  const hasHomeDrawer = document.getElementById("drawer");
  const hasHomeHamburger = document.getElementById("hamburger");

  if (!hasHomeDrawer || !hasHomeHamburger) return;

  /* Call home page render only if it exists */
  if (typeof render === "function") {
    render();
  }
}
/* ─────────────────────────────────────────────
   PEER PROFILE PAGE
───────────────────────────────────────────── */

let bdSelectedRating = 0;
let bdSelectedTags = [];

function initializePeerProfilePage() {
  /* This checks whether we are inside the peer profile page */
  const peerProfilePage = document.getElementById("bd-peer-profile-page");
  if (!peerProfilePage) return;

  initializePeerAvatar();
  initializePeerRatingModal();
}

/* ─────────────────────────────────────────────
   PEER AVATAR
───────────────────────────────────────────── */

function initializePeerAvatar() {
  const avatarElement = document.getElementById("bd-peer-avatar");
  const nameElement = document.getElementById("bd-peer-name");

  if (!avatarElement || !nameElement) return;

  const fullName = nameElement.textContent.trim();
  avatarElement.textContent = getInitialsFromName(fullName);
}

function getInitialsFromName(name) {
  if (!name) return "NA";

  const nameParts = name.trim().split(/\s+/).filter(Boolean);

  if (nameParts.length === 1) {
    return nameParts[0].slice(0, 2).toUpperCase();
  }

  return (
    nameParts[0].charAt(0) +
    nameParts[1].charAt(0)
  ).toUpperCase();
}

/* ─────────────────────────────────────────────
   PEER PROFILE RATING MODAL
───────────────────────────────────────────── */

function initializePeerRatingModal() {
  const rateButton = document.getElementById("bd-peer-rate-button");
  const cancelButton = document.getElementById("bd-rate-cancel-button");
  const submitButton = document.getElementById("bd-rate-submit-button");
  const overlay = document.getElementById("bd-rate-modal-overlay");

  if (rateButton) {
    rateButton.addEventListener("click", openRateModal);
  }

  if (cancelButton) {
    cancelButton.addEventListener("click", closeRateModal);
  }

  if (submitButton) {
    submitButton.addEventListener("click", submitPeerRating);
  }

  if (overlay) {
    overlay.addEventListener("click", closeRateModal);
  }

  initializeRatingStars();
  initializeRatingTags();
}

function openRateModal() {
  const modal = document.getElementById("bd-rate-modal");
  const overlay = document.getElementById("bd-rate-modal-overlay");

  if (modal) modal.classList.add("show");
  if (overlay) overlay.classList.add("show");

  resetRateModal();
}

function closeRateModal() {
  const modal = document.getElementById("bd-rate-modal");
  const overlay = document.getElementById("bd-rate-modal-overlay");

  if (modal) modal.classList.remove("show");
  if (overlay) overlay.classList.remove("show");
}

function resetRateModal() {
  bdSelectedRating = 0;
  bdSelectedTags = [];

  updateSelectedStars(0);

  document.querySelectorAll(".bd-rate-tag-option").forEach(function (tagButton) {
    tagButton.classList.remove("selected");
  });
}

function initializeRatingStars() {
  const stars = document.querySelectorAll(".bd-rate-star");

  if (!stars.length) return;

  stars.forEach(function (star) {
    const value = Number(star.dataset.value);

    star.addEventListener("mouseenter", function () {
      updateHoveredStars(value);
    });

    star.addEventListener("mouseleave", function () {
      updateHoveredStars(0);
      updateSelectedStars(bdSelectedRating);
    });

    star.addEventListener("click", function () {
      bdSelectedRating = value;
      updateSelectedStars(bdSelectedRating);
    });
  });
}

function updateHoveredStars(value) {
  const stars = document.querySelectorAll(".bd-rate-star");

  stars.forEach(function (star) {
    const starValue = Number(star.dataset.value);
    star.classList.toggle("hovered", starValue <= value);
  });
}

function updateSelectedStars(value) {
  const stars = document.querySelectorAll(".bd-rate-star");

  stars.forEach(function (star) {
    const starValue = Number(star.dataset.value);
    star.classList.toggle("active", starValue <= value);
  });
}

function initializeRatingTags() {
  const tagButtons = document.querySelectorAll(".bd-rate-tag-option");

  if (!tagButtons.length) return;

  tagButtons.forEach(function (tagButton) {
    tagButton.addEventListener("click", function () {
      const tag = tagButton.dataset.tag;

      if (bdSelectedTags.includes(tag)) {
        bdSelectedTags = bdSelectedTags.filter(function (item) {
          return item !== tag;
        });
        tagButton.classList.remove("selected");
      } else {
        bdSelectedTags.push(tag);
        tagButton.classList.add("selected");
      }
    });
  });
}

function submitPeerRating() {
  if (bdSelectedRating < 1) {
    alert("Please choose a star rating first.");
    return;
  }

  const ratedStudentInput = document.getElementById("rated-student-id");

  if (!ratedStudentInput) {
    alert("Student ID is missing.");
    return;
  }

  const payload = {
    ratedStudentID: ratedStudentInput.value,
    rating: bdSelectedRating,
    tags: bdSelectedTags
  };

  fetch("save_rating.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify(payload)
  })
    .then(response => response.json())
    .then(data => {
      if (!data.success) {
        alert(data.message || "Could not save rating.");
        return;
      }

      updatePeerRatingUIFromDatabase(data.average, data.count);
      closeRateModal();
showRatingSuccessToast();
location.reload();
    })
    .catch(error => {
      console.error(error);
      alert("Something went wrong while saving the rating.");
    });
}



function updatePeerRatingUIFromDatabase(average, count) {
  const ratingNumber = document.getElementById("bd-peer-rating-number");
  const ratingStars = document.getElementById("bd-peer-rating-stars");
  const ratingReviews = document.getElementById("bd-peer-rating-reviews");
  const ratingCircle = document.getElementById("bd-peer-rating-circle");

  const avg = Number(average);

  if (ratingNumber) {
    ratingNumber.textContent = avg.toFixed(1);
  }

  if (ratingReviews) {
    ratingReviews.textContent = `${count} reviews`;
  }

  if (ratingStars) {
    ratingStars.textContent = buildStarsFromAverage(avg);
  }

  if (ratingCircle) {
    const degree = (avg / 5) * 360;
    ratingCircle.style.background = `conic-gradient(
      var(--teal) 0deg ${degree}deg,
      #e6eef5 ${degree}deg 360deg
    )`;
  }
}





function updatePeerRatingUI(newRating) {
  const ratingNumber = document.getElementById("bd-peer-rating-number");
  const ratingStars = document.getElementById("bd-peer-rating-stars");
  const ratingReviews = document.getElementById("bd-peer-rating-reviews");
  const ratingCircle = document.getElementById("bd-peer-rating-circle");

  const currentAverage = Number(ratingNumber?.textContent || 0);
  const currentReviewText = ratingReviews?.textContent || "0 reviews";
  const currentReviews = extractReviewCount(currentReviewText);

  const newReviewCount = currentReviews + 1;
  const newAverage = ((currentAverage * currentReviews) + newRating) / newReviewCount;
  const roundedAverage = newAverage.toFixed(1);

  if (ratingNumber) {
    ratingNumber.textContent = roundedAverage;
  }

  if (ratingReviews) {
    ratingReviews.textContent = `${newReviewCount} reviews`;
  }

  if (ratingStars) {
    ratingStars.textContent = buildStarsFromAverage(Number(roundedAverage));
  }

  if (ratingCircle) {
    const degree = (Number(roundedAverage) / 5) * 360;
    ratingCircle.style.background = `conic-gradient(
      var(--teal) 0deg ${degree}deg,
      #e6eef5 ${degree}deg 360deg
    )`;
  }
}

function extractReviewCount(text) {
  const match = text.match(/\d+/);
  return match ? Number(match[0]) : 0;
}

function buildStarsFromAverage(average) {
  const filled = Math.round(average);
  let stars = "";

  for (let i = 1; i <= 5; i++) {
    stars += i <= filled ? "★" : "☆";
  }

  return stars;
}

function showRatingSuccessToast() {
  const toast = document.getElementById("bd-rate-success-toast");
  if (!toast) return;

  toast.textContent = "Rating submitted successfully!";
  toast.classList.add("show");

  clearTimeout(toast.hideTimeout);

  toast.hideTimeout = setTimeout(function () {
    toast.classList.remove("show");
  }, 2200);
}


/* ─────────────────────────────────────────────
    Backend
───────────────────────────────────────────── */




/* ─────────────────────────────────────────────
    AJAX LIVE SEARCH
───────────────────────────────────────────── */

function initLiveSearch() {
    const searchInput = document.querySelector('input[name="student_name"]');
    const majorSelect = document.querySelector('select[name="major"]');
    const cardGrid = document.getElementById('cardGrid');
    const countNum = document.getElementById('countNum');
    const clearBtn = document.getElementById('clearBtn');
    
    if (!searchInput || !majorSelect) return;

    const performSearch = () => {
        const query = searchInput.value;
        const major = majorSelect.value;
        
        // 1. Show/Hide Clear button based on input
        if (query.length > 0 || major !== 'all') {
            clearBtn.style.display = 'inline-block';
        } else {
            clearBtn.style.display = 'none';
        }
        
        // 3. Fetch Data
        fetch(`fetch_students.php?student_name=${encodeURIComponent(query)}&major=${major}`)
            .then(response => response.json())
            .then(data => {
                cardGrid.innerHTML = data.html;
                countNum.textContent = data.count;
            })
            .catch(err => console.error("Error fetching students:", err));
    };

    // Trigger on typing (input) and on changing dropdown (change)
    searchInput.addEventListener('input', performSearch);
    majorSelect.addEventListener('change', performSearch);
    
    // Clear Button Logic
    clearBtn.addEventListener('click', () => {
        searchInput.value = '';
        majorSelect.value = 'all';
        performSearch(); // Refresh the grid to "All"
    });

    performSearch(); 
}

// Ensure init is called after DOM is ready
document.addEventListener("DOMContentLoaded", function () {
    initializeHomePage();
    initLiveSearch(); 
});