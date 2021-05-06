describe("Login Page", () => {
    it("should show the login page", () => {
        cy.visit("/").contains("Gemeinde Abfaltersbach");
    });
});
