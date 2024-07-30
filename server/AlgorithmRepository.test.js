const AlgorithmRepository = require('./AlgorithmRepository');

test('should return the latest algorithm', async () => {
  const algorithmRepository = new AlgorithmRepository();
  const latestAlgorithm = await algorithmRepository.getLatestAlgorithm();
  expect(latestAlgorithm.name).toBe('Breadth-First Search (BFS) Algorithm');
});

test('should return algorithm for specific date', async () => {
  const algorithmRepository = new AlgorithmRepository();
  const algorithm = await algorithmRepository.getAlgorithmOfDate('2024-07-28');
  expect(algorithm).toBeTruthy();
});

test('should return all algorithms', async () => {
  const algorithmRepository = new AlgorithmRepository();
  const algorithms = await algorithmRepository.getAlgorithms();
  expect(algorithms.length).toBeGreaterThan(1);
});

test('should check if algorithm exists for specific date', async () => {
  const algorithmRepository = new AlgorithmRepository();
  const exists = await algorithmRepository.existsAlgorithmForDate('2024-07-28');
  expect(exists).toBeTruthy();
});
