<?php
// TermID -> Bitfield value mapping
global $term_bitfield_map;

$term_bitfield_map = array();

// Vocab Email Preferences -> Cheetah Mail fieldname: BEAUTY_INTERESTS
$term_bitfield_map[152] = 1;  // Beauty
$term_bitfield_map[153] = 2;  // Hair Removal
$term_bitfield_map[154] = 4;  // Lips
$term_bitfield_map[155] = 8;  // Nails
$term_bitfield_map[156] = 16; // Skin and Body
$term_bitfield_map[157] = 32; // Tools

// Vocab Fragrance Types -> Cheetah Mail fieldname: FRAGRANCE_TYPES
$term_bitfield_map[172] = 1;   // Eau de Toilette
$term_bitfield_map[173] = 2;   // Eau du Parfum
$term_bitfield_map[174] = 4;   // Perfume
$term_bitfield_map[171] = 8;   // Cologne
$term_bitfield_map[170] = 16;   // After Shave

// Vocab Fragrance Notes -> Cheetah Mail fieldname: FRAGRANCE_NOTES
$term_bitfield_map[158] = 1;   // Aromatic
$term_bitfield_map[159] = 2;   // Floral
$term_bitfield_map[160] = 4;   // Fresh
$term_bitfield_map[161] = 8;   // Oriental
$term_bitfield_map[162] = 16;  // Woody

// Vocab Fragrance Persona -> Cheetah Mail fieldname: FRAGRANCE_PERSONA
$term_bitfield_map[163] = 1;   // Athletic
$term_bitfield_map[164] = 2;   // Dynamic
$term_bitfield_map[165] = 4;   // Earthy
$term_bitfield_map[166] = 8;   // Individualistic
$term_bitfield_map[167] = 16;  // Romantic
$term_bitfield_map[168] = 32;  // Sporty
$term_bitfield_map[169] = 64;  // Traditional