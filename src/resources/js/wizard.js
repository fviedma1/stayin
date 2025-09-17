document.addEventListener("DOMContentLoaded", () => {
    const wizards = document.querySelectorAll(".wizard");

    wizards.forEach((wizard) => {
        const steps = wizard.querySelectorAll(".wizard__form-step");
        const progressSteps = wizard.querySelectorAll(".wizard__progress-step");
        const prevBtn = wizard.querySelector(".wizard__btn--prev");
        const nextBtn = wizard.querySelector(".wizard__btn--next");
        const submitBtn = wizard.querySelector(".wizard__btn--submit");
        let currentStep = 1;
        const totalSteps = progressSteps.length;

        function updateProgress() {
            progressSteps.forEach((step) => {
                step.classList.toggle(
                    "wizard__progress-step--active",
                    parseInt(step.dataset.step) <= currentStep
                );
            });
        }

        function toggleButtons() {
            prevBtn.style.display = currentStep > 1 ? "inline-block" : "none";
            nextBtn.style.display =
                currentStep < totalSteps ? "inline-block" : "none";
            submitBtn.style.display =
                currentStep === totalSteps ? "inline-block" : "none";
        }

        nextBtn.addEventListener("click", () => {
            if (currentStep < totalSteps) {
                wizard
                    .querySelector(
                        `.wizard__form-step[data-step="${currentStep}"]`
                    )
                    .classList.remove("wizard__form-step--active");
                currentStep++;
                wizard
                    .querySelector(
                        `.wizard__form-step[data-step="${currentStep}"]`
                    )
                    .classList.add("wizard__form-step--active");
                toggleButtons();
                updateProgress();
            }
        });

        prevBtn.addEventListener("click", () => {
            if (currentStep > 1) {
                wizard
                    .querySelector(
                        `.wizard__form-step[data-step="${currentStep}"]`
                    )
                    .classList.remove("wizard__form-step--active");
                currentStep--;
                wizard
                    .querySelector(
                        `.wizard__form-step[data-step="${currentStep}"]`
                    )
                    .classList.add("wizard__form-step--active");
                toggleButtons();
                updateProgress();
            }
        });

        // Inicialización
        updateProgress();
        toggleButtons();
    });
});
