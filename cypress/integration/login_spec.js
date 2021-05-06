describe("Homepage", () => {
    beforeEach(() => {
        const now = new Date(2021, 6, 1).getTime();
        cy.clock(now);
    });

    it("should show the Homepage", () => {
        cy.visit("/").contains("Gemeinde Abfaltersbach");
        cy.percySnapshot("Homepage");
    });
});
