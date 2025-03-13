<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Penjualan extends Public_Controller {

    private $pathView = 'report/penjualan/';
    private $url;
    private $hakAkses;

    function __construct()
    {
        parent::__construct();
        $this->url = $this->current_base_uri;
        $this->hakAkses = hakAkses($this->url);
    }

    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/
    /**
     * Default
     */
    public function index()
    {
        if ( $this->hakAkses['a_view'] == 1 ) {
            $this->add_external_js(
                array(
                    "assets/select2/js/select2.min.js",
                    'assets/report/penjualan/js/penjualan.js'
                )
            );
            $this->add_external_css(
                array(
                    "assets/select2/css/select2.min.css",
                    'assets/report/penjualan/css/penjualan.css'
                )
            );
            $data = $this->includes;

            $content['report_harian'] = $this->load->view($this->pathView . 'report_harian', null, TRUE);
            $content['report_harian_produk'] = $this->load->view($this->pathView . 'report_harian_produk', null, TRUE);
            $content['report_by_induk_menu'] = $this->load->view($this->pathView . 'report_by_induk_menu', null, TRUE);
            $content['branch'] = $this->getBranch();
            $content['shift'] = $this->getShift();
            $content['akses'] = $this->hakAkses;

            $data['title_menu'] = 'Laporan Penjualan';
            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);

            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
    }

    public function getBranch()
    {
        $m_branch = new \Model\Storage\Branch_model();
        $d_branch = $m_branch->orderBy('nama', 'asc')->get();

        $data = null;
        if ( $d_branch->count() > 0 ) {
            $data = $d_branch->toArray();
        }

        return $data;
    }

    public function getShift()
    {
        $m_shift = new \Model\Storage\Shift_model();
        $d_shift = $m_shift->orderBy('nama', 'asc')->get();

        $data = null;
        if ( $d_shift->count() > 0 ) {
            $data = $d_shift->toArray();
        }

        return $data;
    }

    public function getLists()
    {
        $params = $this->input->post('params');

        try {
            $shift = $params['shift'];
            $branch = $params['branch'];
            $start_date = $params['start_date'].' 00:00:00';
            $end_date = $params['end_date'].' 23:59:59';

            $m_conf = new \Model\Storage\Conf();
            $sql = "
                select 
                    jl.tgl_trans,
                    jl.kode_faktur,
                    j.kasir,
                    j.nama_kasir,
                    j.member,
                    case
                        when jp.exclude = 1 then
                            ji.total
                        when jp.include = 1 then
                            ji.total - ji.service_charge - ji.ppn
                    end as total,
                    ji.service_charge,
                    ji.ppn,
                    ji.harga,
                    ji.menu_kode,
                    m.nama as nama_menu,
                    jm.nama as nama_jenis_menu,
                    jm.id as id_jenis_menu,
                    ji.jumlah,
                    j.mstatus,
                    sh.id as id_shift,
                    sh.nama as nama_shift
                from jual_item ji
                right join
                    (
                        select
                            _data.kode_branch,
                            max(_data.tgl_trans) as tgl_trans,
                            _data.kode_faktur,
                            max(_data.mstatus) as mstatus
                        from 
                        (
                            select j.branch as kode_branch, j.tgl_trans as tgl_trans, j.kode_faktur as kode_faktur, j.mstatus from jual j where mstatus = 1
                
                            union all
                
                            select j.branch as kode_branch, j.tgl_trans as tgl_trans, jg.faktur_kode_gabungan as kode_faktur, j.mstatus from jual_gabungan jg
                            right join
                                jual j
                                on
                                    jg.faktur_kode = j.kode_faktur
                            where
                                j.mstatus = 1
                        ) _data
                        where
                            _data.tgl_trans between '".$start_date."' and '".$end_date."' and
                            _data.kode_branch = '".$branch."' and
                            _data.kode_faktur is not null
                        group by
                            _data.kode_branch,
                            _data.kode_faktur
                    ) jl
                    on
                        jl.kode_faktur = ji.faktur_kode
                left join
                    jenis_pesanan jp
                    on
                        ji.kode_jenis_pesanan = jp.kode
                left join
                    menu m
                    on
                        ji.menu_kode = m.kode_menu
                left join
                    jenis_menu jm
                    on
                        m.jenis_menu_id = jm.id
                left join
                    jual j
                    on
                        jl.kode_faktur = j.kode_faktur
                left join
                    shift sh
                    on
                        sh.start_time <= SUBSTRING(CONVERT(varchar(max), jl.tgl_trans, 120), 12, 5) and sh.end_time >= SUBSTRING(CONVERT(varchar(max), jl.tgl_trans, 120), 12, 5)
                where
                    sh.id in ('".implode("', '", $shift)."')
            ";
            $d_jual = $m_conf->hydrateRaw( $sql );

            $data = null;
            if ( $d_jual->count() > 0 ) {
                $data = $d_jual->toArray();
            }

            // $mappingDataReportHarian = $this->mappingDataReportHarian( $data, $shift );
            // $mappingDataReportHarianProduk = $this->mappingDataReportHarianProduk( $data, $shift );
            // $mappingDataReportByIndukMenu = $this->mappingDataReportByIndukMenu( $data );
            $mapping = $this->mappingData( $data, $shift );

            $mappingDataReportHarian = $mapping['report_harian'];
            $mappingDataReportHarianProduk = $mapping['report_harian_produk'];
            $mappingDataReportDetailPembayaran = $this->mappingDataReportDetailPembayaran( $start_date, $end_date, $branch, $shift );

            $content_report_harian['data'] = $mappingDataReportHarian;
            $html_report_harian = $this->load->view($this->pathView . 'list_report_harian', $content_report_harian, TRUE);

            $content_report_harian_produk['data'] = $mappingDataReportHarianProduk;
            $html_report_harian_produk = $this->load->view($this->pathView . 'list_report_harian_produk', $content_report_harian_produk, TRUE);

            $content_report_detail_pembayaran['data'] = $mappingDataReportDetailPembayaran;
            $html_report_detail_pembayaran = $this->load->view($this->pathView . 'list_report_detail_pembayaran', $content_report_detail_pembayaran, TRUE);

            $list_html = array(
                'list_report_harian' => $html_report_harian,
                'list_report_harian_produk' => $html_report_harian_produk,
                'list_report_detail_pembayaran' => $html_report_detail_pembayaran
            );

            $this->result['status'] = 1;
            $this->result['content'] = $list_html;
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    function mappingData($_data) {
        $data = null;
        $data_report_harian = null;
        $data_report_harian_produk = null;

        if ( !empty($_data) ) {
            foreach ($_data as $k_data => $v_data) {
                $key_tanggal = str_replace('-', '', substr($v_data['tgl_trans'], 0, 10));

                $key_shift = $v_data['id_shift'];

                /* HARIAN */
                $data_report_harian[ $key_shift ]['id'] = $v_data['id_shift'];
                $data_report_harian[ $key_shift ]['nama'] = $v_data['nama_shift'];

                $key_faktur = $v_data['kode_faktur'];
                $key_kasir = $v_data['kasir'];
                $key_menu = $v_data['menu_kode'];

                $data_report_harian[ $key_shift ]['detail'][ $key_tanggal ]['tanggal'] = substr($v_data['tgl_trans'], 0, 10);
                $data_report_harian[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['nama_kasir'] = $v_data['nama_kasir'];
                $data_report_harian[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['kode_faktur'] = $key_faktur;
                $data_report_harian[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['member'] = $v_data['member'];

                if ( isset($data_report_harian[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['total']) ) {
                    $data_report_harian[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['total'] += $v_data['total'];
                    $data_report_harian[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['service_charge'] += $v_data['service_charge'];
                    $data_report_harian[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['ppn'] += $v_data['ppn'];
                    $data_report_harian[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['grand_total'] += ($v_data['total'] + $v_data['service_charge'] + $v_data['ppn']);
                } else {
                    $data_report_harian[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['total'] = $v_data['total'];
                    $data_report_harian[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['service_charge'] = $v_data['service_charge'];
                    $data_report_harian[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['ppn'] = $v_data['ppn'];
                    $data_report_harian[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['grand_total'] = ($v_data['total'] + $v_data['service_charge'] + $v_data['ppn']);
                }

                if ( !isset($data_report_harian[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]) ) {
                    $data_report_harian[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]['kode'] = $v_data['menu_kode'];
                    $data_report_harian[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]['nama'] = $v_data['nama_menu'];
                    $data_report_harian[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]['harga'] = $v_data['harga'];
                    $data_report_harian[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]['jumlah'] = $v_data['jumlah'];
                    $data_report_harian[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]['total'] = $v_data['total'];
                } else {
                    $data_report_harian[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]['jumlah'] += $v_data['jumlah'];
                    $data_report_harian[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]['total'] += $v_data['total'];
                }
                /* END - HARIAN */

                /* HARIAN PRODUK */
                $data_report_harian_produk[ $key_shift ]['id'] = $v_data['id_shift'];
                $data_report_harian_produk[ $key_shift ]['nama'] = $v_data['nama_shift'];

                $key_jenis = $v_data['id_jenis_menu'];
                $key_menu = $v_data['menu_kode'];
                $data_report_harian_produk[ $key_shift ]['detail'][ $key_jenis ]['id'] = $key_jenis;
                $data_report_harian_produk[ $key_shift ]['detail'][ $key_jenis ]['nama'] = $v_data['nama_jenis_menu'];

                $data_report_harian_produk[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['tanggal'] = substr($v_data['tgl_trans'], 0, 10);
                $data_report_harian_produk[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['kode'] = $key_menu;
                $data_report_harian_produk[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['member'] = $v_data['member'];
                $data_report_harian_produk[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['nama'] = $v_data['nama_menu'];
                $data_report_harian_produk[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['harga'] = $v_data['harga'];

                if ( isset($data_report_harian_produk[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['jumlah']) ) {
                    $data_report_harian_produk[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['jumlah'] += $v_data['jumlah'];
                    $data_report_harian_produk[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['total'] += $v_data['total'];
                    $data_report_harian_produk[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['ppn'] += $v_data['ppn'];
                    $data_report_harian_produk[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['service_charge'] += $v_data['service_charge'];
                    $data_report_harian_produk[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['grand_total'] += ($v_data['total'] + $v_data['service_charge'] + $v_data['ppn']);
                } else {
                    $data_report_harian_produk[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['jumlah'] = $v_data['jumlah'];
                    $data_report_harian_produk[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['total'] = $v_data['total'];
                    $data_report_harian_produk[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['ppn'] = $v_data['ppn'];
                    $data_report_harian_produk[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['service_charge'] = $v_data['service_charge'];
                    $data_report_harian_produk[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['grand_total'] = ($v_data['total'] + $v_data['service_charge'] + $v_data['ppn']);
                }
                /* END - HARIAN PRODUK */
            }
        }

        $data = array(
            'report_harian' => $data_report_harian,
            'report_harian_produk' => $data_report_harian_produk,
        );

        return $data;
    }

    public function mappingDataReportHarian($_data, $_shift)
    {
        $data = null;
        if ( !empty($_data) ) {
            foreach ($_data as $k_data => $v_data) {
                $key_tanggal = str_replace('-', '', substr($v_data['tgl_trans'], 0, 10));

                $key_shift = $v_data['id_shift'];

                $data[ $key_shift ]['id'] = $v_data['id_shift'];
                $data[ $key_shift ]['nama'] = $v_data['nama_shift'];

                $key_faktur = $v_data['kode_faktur'];
                $key_kasir = $v_data['kasir'];
                $key_menu = $v_data['menu_kode'];

                $data[ $key_shift ]['detail'][ $key_tanggal ]['tanggal'] = substr($v_data['tgl_trans'], 0, 10);
                $data[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['nama_kasir'] = $v_data['nama_kasir'];
                $data[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['kode_faktur'] = $key_faktur;
                $data[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['member'] = $v_data['member'];

                if ( isset($data[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['total']) ) {
                    $data[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['total'] += $v_data['total'];
                    $data[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['service_charge'] += $v_data['service_charge'];
                    $data[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['ppn'] += $v_data['ppn'];
                    $data[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['grand_total'] += ($v_data['total'] + $v_data['service_charge'] + $v_data['ppn']);
                } else {
                    $data[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['total'] = $v_data['total'];
                    $data[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['service_charge'] = $v_data['service_charge'];
                    $data[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['ppn'] = $v_data['ppn'];
                    $data[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['grand_total'] = ($v_data['total'] + $v_data['service_charge'] + $v_data['ppn']);
                }

                if ( !isset($data[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]) ) {
                    $data[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]['kode'] = $v_data['menu_kode'];
                    $data[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]['nama'] = $v_data['nama_menu'];
                    $data[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]['harga'] = $v_data['harga'];
                    $data[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]['jumlah'] = $v_data['jumlah'];
                    $data[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]['total'] = $v_data['total'];
                } else {
                    $data[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]['jumlah'] += $v_data['jumlah'];
                    $data[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]['total'] += $v_data['total'];
                }
            }
        }

        return $data;
    }

    public function mappingDataReportHarianProduk($_data, $_shift)
    {
        $data = null;
        if ( !empty($_data) ) {
            foreach ($_data as $k_data => $v_data) {
                $key_tanggal = str_replace('-', '', substr($v_data['tgl_trans'], 0, 10));

                $key_shift = $v_data['id_shift'];

                $data[ $key_shift ]['id'] = $v_data['id_shift'];
                $data[ $key_shift ]['nama'] = $v_data['nama_shift'];

                $key_jenis = $v_data['id_jenis_menu'];
                $key_menu = $v_data['menu_kode'];
                $data[ $key_shift ]['detail'][ $key_jenis ]['id'] = $key_jenis;
                $data[ $key_shift ]['detail'][ $key_jenis ]['nama'] = $v_data['nama_jenis_menu'];

                $data[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['tanggal'] = substr($v_data['tgl_trans'], 0, 10);
                $data[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['kode'] = $key_menu;
                $data[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['member'] = $v_data['member'];
                $data[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['nama'] = $v_data['nama_menu'];
                $data[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['harga'] = $v_data['harga'];

                if ( isset($data[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['jumlah']) ) {
                    $data[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['jumlah'] += $v_data['jumlah'];
                    $data[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['total'] += $v_data['total'];
                    $data[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['ppn'] += $v_data['ppn'];
                    $data[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['service_charge'] += $v_data['service_charge'];
                    $data[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['grand_total'] += ($v_data['total'] + $v_data['service_charge'] + $v_data['ppn']);
                } else {
                    $data[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['jumlah'] = $v_data['jumlah'];
                    $data[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['total'] = $v_data['total'];
                    $data[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['ppn'] = $v_data['ppn'];
                    $data[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['service_charge'] = $v_data['service_charge'];
                    $data[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['grand_total'] = ($v_data['total'] + $v_data['service_charge'] + $v_data['ppn']);
                }
            }
        }

        return $data;
    }

    public function mappingDataReportDetailPembayaran($start_date, $end_date, $branch, $shift)
    {
        $data = null;

        $sql = "
            select data.tgl_trans, data.jenis_bayar, sum(data.nominal) as nominal
            from
            (
                select 
                    jl.*,
                    case
                        when isnull(byr.nominal, 0) = 0 then
                            'PENDING'
                        else
                            byr.jenis_bayar
                    end as jenis_bayar,
                    case
                        when isnull(byr.nominal, 0) = 0 then
                            jl.grand_total
                        else
                            byr.nominal
                    end as nominal,
                    byr.cl,
                    case
                        when isnull(byr.nominal, 0) = 0 then
                            0
                        else
                            jl.grand_total - byr.nominal
                    end as diskon
                from
                (
                    select
                        _data.kode_branch,
                        max(_data.tgl_trans) as tgl_trans,
                        _data.kode_faktur,
                        max(_data.mstatus) as mstatus,
                        sum(_data.grand_total) as grand_total
                    from 
                    (
                        select 
                            j.branch as kode_branch,
                            j.tgl_trans,
                            ji.faktur_kode as kode_faktur,
                            j.mstatus,
                            case
                                when jp.exclude = 1 then
                                    sum(ji.total)
                                when jp.include = 1 then
                                    sum(ji.total - ji.service_charge - ji.ppn)
                            end as total,
                            sum(ji.ppn) as total_ppn,
                            sum(ji.service_charge) as total_service_charge,
                            sum(ji.total) as grand_total
                        from jual_item ji
                        right join
                            jual j
                            on
                                j.kode_faktur = ji.faktur_kode
                        left join
                            jenis_pesanan jp
                            on
                                ji.kode_jenis_pesanan = jp.kode
                        where
                            j.mstatus = 1
                        group by
                            j.branch,
                            j.tgl_trans,
                            ji.faktur_kode,
                            j.mstatus,
                            jp.exclude,
                            jp.include
            
                        union all
            
                        select 
                            j.branch as kode_branch,
                            j.tgl_trans,
                            jg.faktur_kode as kode_faktur,
                            j.mstatus,
                            case
                                when jp.exclude = 1 then
                                    sum(ji.total)
                                when jp.include = 1 then
                                    sum(ji.total - ji.service_charge - ji.ppn)
                            end as total,
                            sum(ji.ppn) as total_ppn,
                            sum(ji.service_charge) as total_service_charge,
                            sum(ji.total) as grand_total
                        from jual_item ji
                        right join
                            jual_gabungan jg
                            on
                                jg.faktur_kode_gabungan = ji.faktur_kode
                        right join
                            jual j
                            on
                                j.kode_faktur = jg.faktur_kode
                        left join
                            jenis_pesanan jp
                            on
                                ji.kode_jenis_pesanan = jp.kode
                        where
                            j.mstatus = 1
                        group by
                            j.branch,
                            j.tgl_trans,
                            jg.faktur_kode,
                            j.mstatus,
                            jp.exclude,
                            jp.include
                    ) _data
                    where
                        _data.tgl_trans between '".$start_date."' and '".$end_date."' and
                        _data.kode_branch = '".$branch."' and
                        _data.kode_faktur is not null
                    group by
                        _data.kode_branch,
                        _data.kode_faktur
                ) jl
                left join
                    (
                        select 
                            bd.id_header,
                            j.kode_faktur,
                            bd.jenis_bayar,
                            bd.kode_jenis_kartu,
                            case
                                when ISNULL(jk.cl, 0) = 0 then
                                    bd.nominal
                                when ISNULL(jk.cl, 0) = 1 then
                                    tagihan.grand_total
                            end as nominal,
                            ISNULL(jk.cl, 0) as cl,
                            j.lunas,
                            tagihan.grand_total as jml_tagihan
                        from bayar_det bd
                        right join
                            jenis_kartu jk
                            on
                                bd.kode_jenis_kartu = jk.kode_jenis_kartu
                        right join
                            (
                                select 
                                    *
                                from (
                                    select 
                                        b.id,
                                        b.faktur_kode, 
                                        b.jml_bayar as jml_bayar,
                                        (b.total - b.diskon) as jml_tagihan
                                    from bayar b 
                                    where 
                                        b.faktur_kode is not null and 
                                        mstatus = 1
            
                                    union all
            
                                    select
                                        b.id,
                                        bh.faktur_kode,
                                        bh.bayar as jml_bayar,
                                        bh.hutang as jml_tagihan
                                    from bayar_hutang bh
                                    right join
                                        bayar b
                                        on
                                            bh.id_header = b.id
                                    where
                                        b.mstatus = 1
                                ) _data
                                where
                                    _data.faktur_kode is not null
                            ) b
                            on
                                b.id = bd.id_header
                        right join
                            jual j
                            on
                                b.faktur_kode = j.kode_faktur
                        right join
                            (
                                select 
                                    j.kode_faktur as faktur_kode,
                                    case
                                        when jp.exclude = 1 then
                                            sum(ji.total)
                                        when jp.include = 1 then
                                            sum(ji.total - ji.service_charge - ji.ppn)
                                    end as total,
                                    sum(ji.ppn) as total_ppn,
                                    sum(ji.service_charge) as total_service_charge,
                                    sum(ji.total) + ISNULL(jg.total, 0) as grand_total
                                from jual_item ji
                                right join
                                    jenis_pesanan jp
                                    on
                                        ji.kode_jenis_pesanan = jp.kode
                                right join
                                    jual j
                                    on
                                        ji.faktur_kode = j.kode_faktur
                                left join
                                    (
                                        select sum(jml_tagihan) as total, faktur_kode from jual_gabungan group by faktur_kode
                                    ) jg
                                    on
                                        j.kode_faktur = jg.faktur_kode
                                where
                                    j.kode_faktur is not null
                                group by
                                    jp.exclude,
                                    jp.include,
                                    j.kode_faktur,
                                    jg.total
                            ) tagihan
                            on
                                tagihan.faktur_kode = j.kode_faktur
                        where
                            bd.jenis_bayar is not null
                    ) byr
                    on
                        byr.kode_faktur = jl.kode_faktur
                    left join
                        shift sh
                        on
                            sh.start_time <= SUBSTRING(CONVERT(varchar(max), jl.tgl_trans, 120), 12, 5) and sh.end_time >= SUBSTRING(CONVERT(varchar(max), jl.tgl_trans, 120), 12, 5)
                    where
                        sh.id in ('".implode("', '", $shift)."')
            ) data
            group by
                data.tgl_trans, data.jenis_bayar
        ";

        $m_conf = new \Model\Storage\Conf();
        $_data = $m_conf->hydrateRaw( $sql );

        if ( $_data->count() > 0 ) {
            $_data = $_data->toArray();
            foreach ($_data as $k_data => $v_data) {
                $key_tanggal = str_replace('-', '', substr($v_data['tgl_trans'], 0, 10));

                $data[ $key_tanggal ]['tanggal'] = substr($v_data['tgl_trans'], 0, 10);

                if ( !isset($data[ $key_tanggal ]['jenis_pembayaran'][ $v_data['jenis_bayar'] ]) ) {
                    $data[ $key_tanggal ]['jenis_pembayaran'][ $v_data['jenis_bayar'] ]['nama'] = $v_data['jenis_bayar'];
                    $data[ $key_tanggal ]['jenis_pembayaran'][ $v_data['jenis_bayar'] ]['total'] = $v_data['nominal'];
                } else {
                    $data[ $key_tanggal ]['jenis_pembayaran'][ $v_data['jenis_bayar'] ]['total'] += $v_data['nominal'];
                }
            }

            if ( !empty($data) ) {
                ksort( $data );
            }
        }

        return $data;
    }

    public function excryptParamsExportExcel()
    {
        $params = $this->input->post('params');

        try {
            $paramsEncrypt = exEncrypt( json_encode($params) );

            $this->result['status'] = 1;
            $this->result['content'] = array('data' => $paramsEncrypt);
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function exportExcel($_params)
    {
        $_data_params = json_decode( exDecrypt( $_params ), true );

        $shift = $_data_params['shift'];
        $branch = $_data_params['branch'];
        $start_date = $_data_params['start_date'].' 00:00:00';
        $end_date = $_data_params['end_date'].' 23:59:59';
        $tipe = $_data_params['tipe'];

        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select 
                jl.tgl_trans,
                jl.kode_faktur,
                j.kasir,
                j.nama_kasir,
                j.member,
                case
                    when jp.exclude = 1 then
                        ji.total
                    when jp.include = 1 then
                        ji.total - ji.service_charge - ji.ppn
                end as total,
                ji.service_charge,
                ji.ppn,
                ji.harga,
                ji.menu_kode,
                m.nama as nama_menu,
                jm.nama as nama_jenis_menu,
                jm.id as id_jenis_menu,
                ji.jumlah,
                j.mstatus,
                sh.id as id_shift,
                sh.nama as nama_shift
            from jual_item ji
            right join
                (
                    select
                        _data.kode_branch,
                        max(_data.tgl_trans) as tgl_trans,
                        _data.kode_faktur,
                        max(_data.mstatus) as mstatus
                    from 
                    (
                        select j.branch as kode_branch, j.tgl_trans as tgl_trans, j.kode_faktur as kode_faktur, j.mstatus from jual j where mstatus = 1
            
                        union all
            
                        select j.branch as kode_branch, j.tgl_trans as tgl_trans, jg.faktur_kode_gabungan as kode_faktur, j.mstatus from jual_gabungan jg
                        right join
                            jual j
                            on
                                jg.faktur_kode = j.kode_faktur
                        where
                            j.mstatus = 1
                    ) _data
                    where
                        _data.tgl_trans between '".$start_date."' and '".$end_date."' and
                        _data.kode_branch = '".$branch."' and
                        _data.kode_faktur is not null
                    group by
                        _data.kode_branch,
                        _data.kode_faktur
                ) jl
                on
                    jl.kode_faktur = ji.faktur_kode
            left join
                jenis_pesanan jp
                on
                    ji.kode_jenis_pesanan = jp.kode
            left join
                menu m
                on
                    ji.menu_kode = m.kode_menu
            left join
                jenis_menu jm
                on
                    m.jenis_menu_id = jm.id
            left join
                jual j
                on
                    jl.kode_faktur = j.kode_faktur
            left join
                shift sh
                on
                    sh.start_time <= SUBSTRING(CONVERT(varchar(max), jl.tgl_trans, 120), 12, 5) and sh.end_time >= SUBSTRING(CONVERT(varchar(max), jl.tgl_trans, 120), 12, 5)
            where
                sh.id in ('".implode("', '", $shift)."')
        ";
        $d_jual = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_jual->count() > 0 ) {
            $data = $d_jual->toArray();
        }

        // $mappingDataReportHarian = $this->mappingDataReportHarian( $data, $shift );
        // $mappingDataReportHarianProduk = $this->mappingDataReportHarianProduk( $data, $shift );
        // $mappingDataReportByIndukMenu = $this->mappingDataReportByIndukMenu( $data );
        $mapping = $this->mappingData( $data, $shift );

        $mappingDataReportHarian = $mapping['report_harian'];
        $mappingDataReportHarianProduk = $mapping['report_harian_produk'];

        $data_pembayaran = $this->mappingDataReportDetailPembayaran( $start_date, $end_date, $branch, $shift );

        $detail = null;
        $nama_view = null;
        $filename = null;
        if ( $tipe == 'harian' ) {
            $detail = $mappingDataReportHarian;
            $nama_view = 'export_excel_harian';
            $filename = 'export-penjualan-harian-'.str_replace('-', '', $_data_params['start_date']).str_replace('-', '', $_data_params['end_date']).'.xls';
        } else {
            $detail = $mappingDataReportHarianProduk;
            $nama_view = 'export_excel_produk';
            $filename = 'export-penjualan-produk-'.str_replace('-', '', $_data_params['start_date']).str_replace('-', '', $_data_params['end_date']).'.xls';
        }


        $data = array(
            'shift' => $shift,
            'branch' => $branch,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'pembayaran' => $data_pembayaran,
            'detail' => $detail
        );

        $content['data'] = $data;
        $res_view_html = $this->load->view('report/penjualan/'.$nama_view, $content, true);

        header("Content-type: application/xls");
        header("Content-Disposition: attachment; filename=".$filename."");
        echo $res_view_html;
    }
}
