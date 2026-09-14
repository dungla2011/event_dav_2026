describe('Family Tree Dimension Changes', () => {
  beforeEach(() => {
    cy.visit('http://127.0.0.1:5500/doing.html');
    cy.get('#nodeWidth').should('be.visible');
    cy.get('rect:not(.link-line)').should('be.visible');
  });

  it('should update node width when slider changes', () => {
    // Get initial state
    cy.get('rect:not(.link-line)').first().invoke('attr', 'width').as('initialWidth');
    cy.get('#nodeWidth').invoke('val').as('initialSliderValue');
    
    // Change width slider
    cy.get('#nodeWidth').clear().type('260');
    cy.get('#nodeWidth').trigger('input');
    
    // Wait for update
    cy.wait(500);
    
    // Check if width changed
    cy.get('rect:not(.link-line)').first().should('have.attr', 'width', '260');
    cy.get('#nodeWidth').should('have.value', '260');
    
    // Verify it's different from initial
    cy.get('@initialWidth').then((initialWidth) => {
      cy.get('rect:not(.link-line)').first().invoke('attr', 'width').should('not.equal', initialWidth);
    });
  });

  it('should update node height when slider changes', () => {
    // Get initial height
    cy.get('rect:not(.link-line)').first().invoke('attr', 'height').as('initialHeight');
    
    // Change height slider
    cy.get('#nodeHeight').clear().type('140');
    cy.get('#nodeHeight').trigger('input');
    
    cy.wait(500);
    
    // Check if height changed
    cy.get('rect:not(.link-line)').first().should('have.attr', 'height', '140');
    cy.get('#nodeHeight').should('have.value', '140');
  });

  it('should update global variables', () => {
    // Change width
    cy.get('#nodeWidth').clear().type('320');
    cy.get('#nodeWidth').trigger('input');
    
    // Check global variable
    cy.window().its('defaultNodeWidth').should('equal', 320);
    
    // Change height
    cy.get('#nodeHeight').clear().type('180');
    cy.get('#nodeHeight').trigger('input');
    
    cy.window().its('defaultNodeHeight').should('equal', 180);
  });

  it('should persist settings in localStorage', () => {
    // Change dimensions
    cy.get('#nodeWidth').clear().type('290');
    cy.get('#nodeWidth').trigger('input');
    
    cy.wait(500);
    
    // Check localStorage
    cy.window().then((win) => {
      const config = JSON.parse(win.localStorage.getItem('familyTree_globalConfig'));
      expect(config.nodeWidth).to.equal(290);
    });
    
    // Reload and check persistence
    cy.reload();
    cy.get('#nodeWidth').should('have.value', '290');
  });

  it('should handle multiple nodes consistently', () => {
    // Change width
    cy.get('#nodeWidth').clear().type('310');
    cy.get('#nodeWidth').trigger('input');
    
    cy.wait(500);
    
    // Check that at least some rects have the new width
    cy.get('rect:not(.link-line)').then(($rects) => {
      const widths = Array.from($rects).map(rect => rect.getAttribute('width'));
      expect(widths.some(width => width === '310')).to.be.true;
    });
  });
});
