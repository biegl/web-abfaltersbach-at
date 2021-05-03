describe("Login Page", () => {
    it("should show the login page", () => {
        cy.visit("localhost").contains("Gemeinde Abfaltersbach");
    });
});
