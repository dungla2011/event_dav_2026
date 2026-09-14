// Test data for multiple wives scenario
const testData = {
    "name": "John Smith",
    "id": "john-1",
    "x": 400,
    "y": 100,
    "children": [
        {
            "name": "Alice Smith",
            "id": "alice-1", 
            "x": 200,
            "y": 200,
            "child_of_second_married": false // First wife's child
        },
        {
            "name": "Bob Smith",
            "id": "bob-1",
            "x": 400,
            "y": 200,
            "child_of_second_married": true // Second wife's child - should get dashed line
        },
        {
            "name": "Carol Smith", 
            "id": "carol-1",
            "x": 600,
            "y": 200,
            "child_of_second_married": true // Second wife's child - should get dashed line
        }
    ],
    "spouses": [
        {
            "name": "Mary Johnson", 
            "id": "mary-1",
            "x": 350,
            "y": 150,
            "marriage_order": 1
        },
        {
            "name": "Susan Brown",
            "id": "susan-1", 
            "x": 450,
            "y": 150,
            "marriage_order": 2
        }
    ]
};

console.log("Test data for multiple wives scenario:");
console.log("- John has 2 wives: Mary (first) and Susan (second)");
console.log("- Alice is from first marriage (solid line)");
console.log("- Bob and Carol are from second marriage (dashed lines from midpoint between wives)");
console.log("- Expected: Links to Bob and Carol should start from midpoint between Mary and Susan");
console.log("- Expected: Links to Bob and Carol should have stroke-dasharray: '5,5'");
