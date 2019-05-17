<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Plugin lang file: French.
 *
 * @package    local_uca_transfer_course
 * @author     Université Clermont Auvergne - Anthony Durif
 * @copyright  2018 Université Clermont Auvergne
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Transfert de cours';
$string['transfer_request'] = 'Demande de transfert de cours';
$string['transfer_histo'] = 'Historique des demandes';
$string['transfer_list'] = 'Vos cours disponibles';
$string['transfer_requests'] = 'Vos demandes';
$string['no_request'] = 'Aucune demande de transfert.';
$string['request_date'] = 'Date de la demande';
$string['course_transfer_url'] = 'Url du json listant les cours que l\'on peut transferer';
$string['course_transfer_url_desc'] = 'Url à laquelle se trouve le fichier json qui liste les cours de votre ancienne plateforme que vous pouvez transferer';
$string['course_export_url'] = 'Url pour lancer le transfert de cours';
$string['course_export_url_desc'] = 'Url de la commande permettant le transfert des cours';
$string['transfer_archives_folder'] = 'Dossier où sont générées les archives de cours';
$string['transfer_archives_folder_desc'] = 'Chemin du dossier où sont générées et déposées les archives des cours à transferer de votre ancien moodle.';
$string['no_course'] = 'Vous ne pouvez transférer aucun cours.';
$string['transfer'] = 'Transférer les cours sélectionnés';
$string['to_transfer'] = 'Cours à transférer';
$string['cancel_transfer'] = 'Annuler la demande';
$string['transfer_complete'] = 'Cours transféré le ';
$string['transfer_in_progress'] = 'En cours, débuté le ';
$string['target'] = 'Catégorie où déplacer le cours: ';
$string['privacy:metadata'] = 'Ce plugin enregistre en base de données les demandes de transfert de cours depuis une autre plateforme faites par l\'utilisateur.';
$string['privacy:metadata:userid'] = 'L\'identifiant de l\'utilisateur qui a fait la demande.';
$string['privacy:metadata:courseid'] = 'L\'identifiant du cours concerné par la demande.';
$string['privacy:metadata:categoryid'] = 'L\'identifiant de la catégorie de cours dans laquelle le cours sera transféré.';
$string['privacy:metadata:list'] = 'Liste des demandes effectuées par l\utilisateur %s:\n';
$string['privacy:metadata:request'] = 'Cours "%s" dans la catégorie "%s".\n';
$string['privacy:metadata:empty'] = 'Pas de données à exporter.';