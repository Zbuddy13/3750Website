-- 2
-- Display the names of all the bands as Band Name
SELECT * from bands;
SELECT bands.name AS 'Band Name' FROM bands;
-- 3
-- Return only the oldest album
SELECT * from albums;
SELECT * FROM albums WHERE release_year IS NOT NULL ORDER BY release_year ASC LIMIT 1;
-- 4 
-- Return the bands that have albums
SELECT * from bands;
SELECT * from albums;
SELECT DISTINCT bands.name as 'Band Name' FROM bands
JOIN albums ON albums.band_id=bands.id;
-- 5
-- Return the bands that do not have albums
SELECT DISTINCT bands.name AS 'Band Name' FROM bands
LEFT JOIN albums ON bands.id = albums.band_id
WHERE albums.id IS NULL;
-- 6
-- Return the album with the longest duration
SELECT
  albums.name as Name,
  albums.release_year as 'Release Year',
  SUM(songs.length) as 'Duration'
FROM albums
JOIN songs on albums.id = songs.album_id
GROUP BY songs.album_id
ORDER BY Duration DESC
LIMIT 1;
-- 7
-- Update the release year of the album with no release year
SELECT * FROM albums where release_year IS NULL;
UPDATE albums
SET release_year = 1986
WHERE id = 4;
-- 8
-- Insert my favorite band and album
INSERT INTO bands (name)
VALUES ('Foreigner');
SELECT id FROM bands ORDER BY id DESC LIMIT 1;
INSERT INTO albums (name, release_year, band_id)
VALUES ('4', 1981, 8);
-- 9
-- Delete the band I added
SELECT id FROM albums ORDER BY id DESC LIMIT 1;
DELETE FROM albums WHERE id = 19;
DELETE FROM bands WHERE id = 8;
-- 10
-- Average song length
SELECT AVG(length) as 'Average Song Duration'
FROM songs;
-- 11
-- Longest song from each album
SELECT
  albums.name AS 'Album',
  albums.release_year AS 'Release Year',
  MAX(songs.length) AS 'Duration'
FROM albums
JOIN songs ON albums.id = songs.album_id
GROUP BY songs.album_id;
-- 12
-- Number of songs per band
SELECT
  bands.name AS 'Band',
  COUNT(songs.id) AS 'Number of Songs'
FROM bands
JOIN albums ON bands.id = albums.band_id
JOIN songs ON albums.id = songs.album_id
GROUP BY albums.band_id;