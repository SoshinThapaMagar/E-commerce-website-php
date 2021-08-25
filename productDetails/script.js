const allergyTitle = document.querySelector('.allergy-title');
const allergySVG = document.querySelector('.allergy-arrow');
const allergyContent = document.querySelector('.allergy-content');
const allergyContainer = document.querySelector('.allergy-info-container');

allergyTitle.addEventListener('click', toggleAllergy);
allergySVG.addEventListener('click', toggleAllergy);

function toggleAllergy() {
    allergyTitle.classList.toggle('allergy-title-expanded');
    allergyContent.classList.toggle('allergy-content-expanded');
    allergySVG.classList.toggle('arrow-expanded');
    allergyContainer.classList.toggle('allergy-info-container-expanded');
}
