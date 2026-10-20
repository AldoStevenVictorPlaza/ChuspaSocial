<?php
// This file is part of Moodle - http://moodle.org
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://gnu.org>.

/**
 * Pruebas unitarias para listing_service::delete_listing().
 *
 * @package    local_chuspasocial
 * @category   test
 * @copyright  2026 sis-inf
 * @license    http://gnu.org GNU GPL v3 or later
 */

namespace local_chuspasocial\local;

defined('MOODLE_INTERNAL') || die();

use advanced_testcase;

/**
 * Clase de pruebas para el servicio de anuncios.
 *
 * @package    local_chuspasocial
 * @category   test
 * @copyright  2026 sis-inf
 * @license    http://gnu.org GNU GPL v3 or later
 * @covers     \local_chuspasocial\local\listing_service::delete_listing
 */
class listing_service_test extends advanced_testcase {

    /**
     * Configuración inicial antes de cada prueba.
     *
     * @return void
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);
    }

    /**
     * Prueba que el autor original de un anuncio puede borrarlo con éxito.
     */
    public function test_delete_listing_as_author(): void {
        global $DB;

        $generator = $this->getDataGenerator();
        $author = $generator->create_user();

        $plugingenerator = $generator->get_plugin_generator('local_chuspasocial');
        $listing = $plugingenerator->create_listing(['userid' => $author->id]);

        $this->setUser($author);

        $result = listing_service::delete_listing($listing->id);
        $this->assertTrue($result);

        $this->assertFalse($DB->record_exists('local_chuspasocial_listing', ['id' => $listing->id]));
    }

    /**
     * Prueba que un usuario con rol de moderador puede borrar cualquier anuncio.
     */
    public function test_delete_listing_as_moderator(): void {
        global $DB;

        $generator = $this->getDataGenerator();
        $author = $generator->create_user();
        $moderator = $generator->create_user();

        $plugingenerator = $generator->get_plugin_generator('local_chuspasocial');
        $listing = $plugingenerator->create_listing(['userid' => $author->id]);

        $roleid = $generator->create_role();
        assign_capability('local/chuspasocial:moderate', CAP_ALLOW, $roleid, \context_system::instance()->id);
        role_assign($roleid, $moderator->id, \context_system::instance()->id);

        $this->setUser($moderator);

        $result = listing_service::delete_listing($listing->id);
        $this->assertTrue($result);

        $this->assertFalse($DB->record_exists('local_chuspasocial_listing', ['id' => $listing->id]));
    }

    /**
     * Prueba que al borrar un anuncio también se eliminan sus vínculos asociados.
     */
    public function test_delete_listing_removes_links(): void {
        global $DB;

        $generator = $this->getDataGenerator();
        $author = $generator->create_user();
        $course = $generator->create_course();

        $plugingenerator = $generator->get_plugin_generator('local_chuspasocial');
        $listing = $plugingenerator->create_listing(['userid' => $author->id]);

        $linkid = $DB->insert_record('local_chuspasocial_lstcourse', (object) [
            'listingid' => $listing->id,
            'courseid' => $course->id,
            'timecreated' => time()
        ]);

        $this->setUser($author);

        $result = listing_service::delete_listing($listing->id);
        $this->assertTrue($result);

        $this->assertFalse($DB->record_exists('local_chuspasocial_listing', ['id' => $listing->id]));
        $this->assertFalse($DB->record_exists('local_chuspasocial_lstcourse', ['id' => $linkid]));
    }
    /**
     * Crea un anuncio de libro de prueba.
     *
     * @param string $title Título del libro.
     * @param string $author Autor del libro.
     * @return int Id del anuncio creado.
     */
    private function crear_anuncio(string $title, string $author): int {
        global $DB;
        $user = $this->getDataGenerator()->create_user();
        return $DB->insert_record('local_chuspasocial_listing', (object) [
            'userid' => $user->id,
            'title' => $title,
            'author' => $author,
            'status' => 'available',
            'timecreated' => time(),
            'timemodified' => time(),
        ]);
    }

    /**
     * Busca por título.
     *
     * @covers \local_chuspasocial\local\listing_service::search
     */
    public function test_search_por_titulo(): void {
        $id = $this->crear_anuncio('Cálculo I', 'Stewart');
        $this->crear_anuncio('Física', 'Serway');

        $result = listing_service::search('Cálculo', 0, 10);

        $this->assertCount(1, $result);
        $this->assertEquals($id, $result[0]->id);
    }

    /**
     * Busca por autor.
     *
     * @covers \local_chuspasocial\local\listing_service::search
     */
    public function test_search_por_autor(): void {
        $id = $this->crear_anuncio('Física', 'Serway');
        $this->crear_anuncio('Cálculo I', 'Stewart');

        $result = listing_service::search('Serway', 0, 10);

        $this->assertCount(1, $result);
        $this->assertEquals($id, $result[0]->id);
    }

    /**
     * La búsqueda no distingue mayúsculas de minúsculas.
     *
     * @covers \local_chuspasocial\local\listing_service::search
     */
    public function test_search_ignora_mayusculas(): void {
        $id = $this->crear_anuncio('Programación Web', 'Pérez');

        $result = listing_service::search('PROGRAMACIÓN web', 0, 10);

        $this->assertCount(1, $result);
        $this->assertEquals($id, $result[0]->id);
    }

    /**
     * Los caracteres especiales se buscan como texto normal.
     *
     * @covers \local_chuspasocial\local\listing_service::search
     */
    public function test_search_caracteres_especiales(): void {
        $id = $this->crear_anuncio('100% Álgebra', 'Baldor');
        $this->crear_anuncio('100 problemas', 'Gómez');

        $result = listing_service::search('100%', 0, 10);

        $this->assertCount(1, $result);
        $this->assertEquals($id, $result[0]->id);
    }

    /**
     * Inserta un anuncio con timemodified antiguo para el autor indicado.
     *
     * @param int $userid Id del autor.
     * @return int Id del anuncio creado.
     */
    private function crear_anuncio_antiguo(int $userid): int {
        global $DB;
        return $DB->insert_record('local_chuspasocial_listing', (object) [
            'userid' => $userid,
            'title' => 'Titulo original',
            'author' => 'Autor original',
            'status' => 'available',
            'timecreated' => time() - 100,
            'timemodified' => time() - 100,
        ]);
    }

    /**
     * El autor puede actualizar su anuncio y se actualiza timemodified.
     *
     * @covers \local_chuspasocial\local\listing_service::update_listing
     */
    public function test_update_listing_as_author(): void {
        global $DB;

        $author = $this->getDataGenerator()->create_user();
        $id = $this->crear_anuncio_antiguo((int) $author->id);
        $before = (int) $DB->get_field('local_chuspasocial_listing', 'timemodified', ['id' => $id]);

        $this->setUser($author);
        $result = listing_service::update_listing($id, (object) ['title' => 'Titulo nuevo']);

        $this->assertInstanceOf(\local_chuspasocial\local\persistent\listing::class, $result);
        $this->assertEquals('Titulo nuevo', $result->get('title'));

        $record = $DB->get_record('local_chuspasocial_listing', ['id' => $id], '*', MUST_EXIST);
        $this->assertEquals('Titulo nuevo', $record->title);
        $this->assertEquals('Autor original', $record->author);
        $this->assertGreaterThan($before, (int) $record->timemodified);
    }

    /**
     * Otro usuario recibe una excepcion y el anuncio no cambia.
     *
     * @covers \local_chuspasocial\local\listing_service::update_listing
     */
    public function test_update_listing_as_other_user_throws(): void {
        global $DB;

        $author = $this->getDataGenerator()->create_user();
        $other = $this->getDataGenerator()->create_user();
        $id = $this->crear_anuncio_antiguo((int) $author->id);

        $this->setUser($other);

        try {
            listing_service::update_listing($id, (object) ['title' => 'Titulo hackeado']);
            $this->fail('Se esperaba una moodle_exception.');
        } catch (\moodle_exception $e) {
            $this->assertEquals('Titulo original', $DB->get_field('local_chuspasocial_listing', 'title', ['id' => $id]));
        }
    }

    /**
     * El autor no puede cambiar el propietario del anuncio.
     *
     * @covers \local_chuspasocial\local\listing_service::update_listing
     */
    public function test_update_listing_ignores_protected_fields(): void {
        global $DB;

        $author = $this->getDataGenerator()->create_user();
        $other = $this->getDataGenerator()->create_user();
        $id = $this->crear_anuncio_antiguo((int) $author->id);

        $this->setUser($author);
        listing_service::update_listing($id, (object) ['userid' => $other->id, 'title' => 'Otro titulo']);

        $record = $DB->get_record('local_chuspasocial_listing', ['id' => $id], '*', MUST_EXIST);
        $this->assertEquals($author->id, $record->userid);
        $this->assertEquals('Otro titulo', $record->title);
    }}
