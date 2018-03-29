<?php
namespace app\models;

class CountryCode {
	public static $COUNTRY_MAP = array (
			0 =>
			array (
					'id' => 0,
					'cn' => '亚洲',
					'en' => 'Asia',
			),
			101 =>
			array (
					'id' => 101,
					'cn' => '阿富汗',
					'en' => 'Afghanistan',
			),
			102 =>
			array (
					'id' => 102,
					'cn' => '巴林',
					'en' => 'Bahrian',
			),
			103 =>
			array (
					'id' => 103,
					'cn' => '孟加拉国',
					'en' => 'Bangladesh',
			),
			104 =>
			array (
					'id' => 104,
					'cn' => '不丹',
					'en' => 'Bhutan',
			),
			105 =>
			array (
					'id' => 105,
					'cn' => '文莱',
					'en' => 'Brunei',
			),
			106 =>
			array (
					'id' => 106,
					'cn' => '缅甸',
					'en' => 'Myanmar',
			),
			107 =>
			array (
					'id' => 107,
					'cn' => '柬埔寨',
					'en' => 'Cambodia',
			),
			108 =>
			array (
					'id' => 108,
					'cn' => '塞浦路斯',
					'en' => 'Cyprus',
			),
			109 =>
			array (
					'id' => 109,
					'cn' => '朝鲜民主主义人民共和国',
					'en' => 'Korea,DPR',
			),
			110 =>
			array (
					'id' => 110,
					'cn' => '香港',
					'en' => 'Hong Kong',
			),
			111 =>
			array (
					'id' => 111,
					'cn' => '印度',
					'en' => 'India',
			),
			112 =>
			array (
					'id' => 112,
					'cn' => '印度尼西亚',
					'en' => 'Indonesia',
			),
			113 =>
			array (
					'id' => 113,
					'cn' => '伊朗',
					'en' => 'Iran',
			),
			114 =>
			array (
					'id' => 114,
					'cn' => '伊拉克',
					'en' => 'Iraq',
			),
			115 =>
			array (
					'id' => 115,
					'cn' => '以色列',
					'en' => 'Israel',
			),
			116 =>
			array (
					'id' => 116,
					'cn' => '日本',
					'en' => 'Japan',
			),
			117 =>
			array (
					'id' => 117,
					'cn' => '约旦',
					'en' => 'Jordan',
			),
			118 =>
			array (
					'id' => 118,
					'cn' => '科威特',
					'en' => 'Kuwait',
			),
			119 =>
			array (
					'id' => 119,
					'cn' => '老挝',
					'en' => 'Laos,PDR',
			),
			120 =>
			array (
					'id' => 120,
					'cn' => '黎巴嫩',
					'en' => 'Lebanon',
			),
			121 =>
			array (
					'id' => 121,
					'cn' => '澳门',
					'en' => 'Macau',
			),
			122 =>
			array (
					'id' => 122,
					'cn' => '马来西亚',
					'en' => 'Malaysia',
			),
			123 =>
			array (
					'id' => 123,
					'cn' => '马尔代夫',
					'en' => 'Maldives',
			),
			124 =>
			array (
					'id' => 124,
					'cn' => '蒙古',
					'en' => 'Mongolia',
			),
			125 =>
			array (
					'id' => 125,
					'cn' => '尼泊尔',
					'en' => 'Nepal',
			),
			126 =>
			array (
					'id' => 126,
					'cn' => '阿曼',
					'en' => 'Oman',
			),
			127 =>
			array (
					'id' => 127,
					'cn' => '巴基斯坦',
					'en' => 'Pakistan',
			),
			128 =>
			array (
					'id' => 128,
					'cn' => '巴勒斯坦',
					'en' => 'Palestine',
			),
			129 =>
			array (
					'id' => 129,
					'cn' => '菲律宾',
					'en' => 'Philippines',
			),
			130 =>
			array (
					'id' => 130,
					'cn' => '卡塔尔',
					'en' => 'Qatar',
			),
			131 =>
			array (
					'id' => 131,
					'cn' => '沙特阿拉伯',
					'en' => 'Saudi Arabia',
			),
			132 =>
			array (
					'id' => 132,
					'cn' => '新加坡',
					'en' => 'Singapore',
			),
			133 =>
			array (
					'id' => 133,
					'cn' => '韩国',
					'en' => 'Korea Rep.',
			),
			134 =>
			array (
					'id' => 134,
					'cn' => '斯里兰卡',
					'en' => 'Sri Lanka',
			),
			135 =>
			array (
					'id' => 135,
					'cn' => '叙利亚',
					'en' => 'Syrian',
			),
			136 =>
			array (
					'id' => 136,
					'cn' => '泰国',
					'en' => 'Thailand',
			),
			137 =>
			array (
					'id' => 137,
					'cn' => '土耳其',
					'en' => 'Turkey',
			),
			138 =>
			array (
					'id' => 138,
					'cn' => '阿拉伯联合酋长国',
					'en' => 'United Arab Emirates',
			),
			139 =>
			array (
					'id' => 139,
					'cn' => '也门共和国',
					'en' => 'Republic of Yemen',
			),
			141 =>
			array (
					'id' => 141,
					'cn' => '越南',
					'en' => 'Vietnam',
			),
			142 =>
			array (
					'id' => 142,
					'cn' => '中华人民共和国',
					'en' => 'China',
			),
			143 =>
			array (
					'id' => 143,
					'cn' => '台澎金马关税区',
					'en' => 'Taiwan prov. Of china',
			),
			144 =>
			array (
					'id' => 144,
					'cn' => '东帝汶',
					'en' => 'Timor-Leste',
			),
			145 =>
			array (
					'id' => 145,
					'cn' => '哈沙克斯坦',
					'en' => 'Kazakhstan',
			),
			146 =>
			array (
					'id' => 146,
					'cn' => '吉尔吉斯斯坦',
					'en' => 'Kyrgyzstan',
			),
			147 =>
			array (
					'id' => 147,
					'cn' => '塔吉克斯坦',
					'en' => 'Tadzhikistan',
			),
			148 =>
			array (
					'id' => 148,
					'cn' => '土库曼斯坦',
					'en' => 'Tajikistan',
			),
			149 =>
			array (
					'id' => 149,
					'cn' => '乌兹别克斯坦',
					'en' => 'Uzbekist',
			),
			199 =>
			array (
					'id' => 199,
					'cn' => '亚洲其他国家(地区)',
					'en' => 'Oth. Asia nes',
			),
			200 =>
			array (
					'id' => 200,
					'cn' => '非洲',
					'en' => 'Africa',
			),
			201 =>
			array (
					'id' => 201,
					'cn' => '阿尔及利亚',
					'en' => 'Algeria',
			),
			202 =>
			array (
					'id' => 202,
					'cn' => '安哥拉',
					'en' => 'Angora',
			),
			203 =>
			array (
					'id' => 203,
					'cn' => '贝宁',
					'en' => 'Benin',
			),
			204 =>
			array (
					'id' => 204,
					'cn' => '博茨瓦那',
					'en' => 'Botswana',
			),
			205 =>
			array (
					'id' => 205,
					'cn' => '布隆迪',
					'en' => 'Burundi',
			),
			206 =>
			array (
					'id' => 206,
					'cn' => '喀麦隆',
					'en' => 'Cameroon',
			),
			207 =>
			array (
					'id' => 207,
					'cn' => '加那利群岛',
					'en' => 'Canary Is',
			),
			208 =>
			array (
					'id' => 208,
					'cn' => '佛得角',
					'en' => 'Cape Vrde',
			),
			209 =>
			array (
					'id' => 209,
					'cn' => '中非',
					'en' => 'Central African Rep.',
			),
			210 =>
			array (
					'id' => 210,
					'cn' => '塞卜泰(休达)',
					'en' => 'Ceuta',
			),
			211 =>
			array (
					'id' => 211,
					'cn' => '乍得',
					'en' => 'Chad',
			),
			212 =>
			array (
					'id' => 212,
					'cn' => '科摩罗',
					'en' => 'Comoros',
			),
			213 =>
			array (
					'id' => 213,
					'cn' => '刚果',
					'en' => 'Congo',
			),
			214 =>
			array (
					'id' => 214,
					'cn' => '吉布提',
					'en' => 'Djibouti',
			),
			215 =>
			array (
					'id' => 215,
					'cn' => '埃及',
					'en' => 'Egypt',
			),
			216 =>
			array (
					'id' => 216,
					'cn' => '赤道几内亚',
					'en' => 'Eq.Guinea',
			),
			217 =>
			array (
					'id' => 217,
					'cn' => '埃塞俄比亚',
					'en' => 'Ethiopia',
			),
			218 =>
			array (
					'id' => 218,
					'cn' => '加蓬',
					'en' => 'Gabon',
			),
			219 =>
			array (
					'id' => 219,
					'cn' => '冈比亚',
					'en' => 'Gambia',
			),
			220 =>
			array (
					'id' => 220,
					'cn' => '加纳',
					'en' => 'Ghana',
			),
			221 =>
			array (
					'id' => 221,
					'cn' => '几内亚',
					'en' => 'Guinea',
			),
			222 =>
			array (
					'id' => 222,
					'cn' => '几内亚(比绍)',
					'en' => 'Guinea Bissau',
			),
			223 =>
			array (
					'id' => 223,
					'cn' => '科特迪瓦',
					'en' => 'Cote d\'lvoir',
			),
			224 =>
			array (
					'id' => 224,
					'cn' => '肯尼亚',
					'en' => 'Kenya',
			),
			225 =>
			array (
					'id' => 225,
					'cn' => '利比里亚',
					'en' => 'Liberia',
			),
			226 =>
			array (
					'id' => 226,
					'cn' => '利比亚',
					'en' => 'Libyan Arab Jm',
			),
			227 =>
			array (
					'id' => 227,
					'cn' => '马达加斯加',
					'en' => 'Madagascar',
			),
			228 =>
			array (
					'id' => 228,
					'cn' => '马拉维',
					'en' => 'Malawi',
			),
			229 =>
			array (
					'id' => 229,
					'cn' => '马里',
					'en' => 'Mali',
			),
			230 =>
			array (
					'id' => 230,
					'cn' => '毛里塔尼亚',
					'en' => 'Mauritania',
			),
			231 =>
			array (
					'id' => 231,
					'cn' => '毛里求斯',
					'en' => 'Mauritius',
			),
			232 =>
			array (
					'id' => 232,
					'cn' => '摩洛哥',
					'en' => 'Morocco',
			),
			233 =>
			array (
					'id' => 233,
					'cn' => '莫桑比克',
					'en' => 'Mozambique',
			),
			234 =>
			array (
					'id' => 234,
					'cn' => '纳米比亚',
					'en' => 'Namibia',
			),
			235 =>
			array (
					'id' => 235,
					'cn' => '尼日尔',
					'en' => 'Niger',
			),
			236 =>
			array (
					'id' => 236,
					'cn' => '尼日利亚',
					'en' => 'Nigeria',
			),
			237 =>
			array (
					'id' => 237,
					'cn' => '留尼汪',
					'en' => 'Reunion',
			),
			238 =>
			array (
					'id' => 238,
					'cn' => '卢旺达',
					'en' => 'Rwanda',
			),
			239 =>
			array (
					'id' => 239,
					'cn' => '圣多美和普林西比',
					'en' => 'Sao Tome & Principe',
			),
			240 =>
			array (
					'id' => 240,
					'cn' => '塞内加尔',
					'en' => 'Senegal',
			),
			241 =>
			array (
					'id' => 241,
					'cn' => '塞舌尔',
					'en' => 'Seychelles',
			),
			242 =>
			array (
					'id' => 242,
					'cn' => '塞拉利昂',
					'en' => 'Sierra Leone',
			),
			243 =>
			array (
					'id' => 243,
					'cn' => '索马里',
					'en' => 'Somalia',
			),
			244 =>
			array (
					'id' => 244,
					'cn' => '南非(阿扎尼亚)',
					'en' => 'S.Africa',
			),
			245 =>
			array (
					'id' => 245,
					'cn' => '西撒哈拉',
					'en' => 'Western Sahara',
			),
			246 =>
			array (
					'id' => 246,
					'cn' => '苏丹',
					'en' => 'Sudan',
			),
			247 =>
			array (
					'id' => 247,
					'cn' => '坦桑尼亚',
					'en' => 'Tanzania',
			),
			248 =>
			array (
					'id' => 248,
					'cn' => '多哥',
					'en' => 'Togo',
			),
			249 =>
			array (
					'id' => 249,
					'cn' => '突尼斯',
					'en' => 'Tunisia',
			),
			250 =>
			array (
					'id' => 250,
					'cn' => '乌干达',
					'en' => 'Uganda',
			),
			251 =>
			array (
					'id' => 251,
					'cn' => '布基纳法索',
					'en' => 'Burkina Faso',
			),
			252 =>
			array (
					'id' => 252,
					'cn' => '民主刚果',
					'en' => 'Congo,DR',
			),
			253 =>
			array (
					'id' => 253,
					'cn' => '赞比亚',
					'en' => 'Zambia',
			),
			254 =>
			array (
					'id' => 254,
					'cn' => '津巴布韦',
					'en' => 'Zimbabwe',
			),
			255 =>
			array (
					'id' => 255,
					'cn' => '莱索托',
					'en' => 'Lesotho',
			),
			256 =>
			array (
					'id' => 256,
					'cn' => '梅利利亚',
					'en' => 'Melilla',
			),
			257 =>
			array (
					'id' => 257,
					'cn' => '斯威士兰',
					'en' => 'Swaziland',
			),
			258 =>
			array (
					'id' => 258,
					'cn' => '厄立特里亚',
					'en' => 'Eritrea',
			),
			299 =>
			array (
					'id' => 299,
					'cn' => '非洲其他国家(地区)',
					'en' => 'Oth. Afr. nes',
			),
			300 =>
			array (
					'id' => 300,
					'cn' => '欧洲',
					'en' => 'Europe',
			),
			301 =>
			array (
					'id' => 301,
					'cn' => '比利时',
					'en' => 'Belgium',
			),
			302 =>
			array (
					'id' => 302,
					'cn' => '丹麦',
					'en' => 'Denmark',
			),
			303 =>
			array (
					'id' => 303,
					'cn' => '英国',
					'en' => 'United Kingdom',
			),
			304 =>
			array (
					'id' => 304,
					'cn' => '德国',
					'en' => 'Germany',
			),
			305 =>
			array (
					'id' => 305,
					'cn' => '法国',
					'en' => 'France',
			),
			306 =>
			array (
					'id' => 306,
					'cn' => '爱尔兰',
					'en' => 'Ireland',
			),
			307 =>
			array (
					'id' => 307,
					'cn' => '意大利',
					'en' => 'Italy',
			),
			308 =>
			array (
					'id' => 308,
					'cn' => '卢森堡',
					'en' => 'Luxembourg',
			),
			309 =>
			array (
					'id' => 309,
					'cn' => '荷兰',
					'en' => 'Netherlands',
			),
			310 =>
			array (
					'id' => 310,
					'cn' => '希腊',
					'en' => 'Greece',
			),
			311 =>
			array (
					'id' => 311,
					'cn' => '葡萄牙',
					'en' => 'Portugal',
			),
			312 =>
			array (
					'id' => 312,
					'cn' => '西班牙',
					'en' => 'Spain',
			),
			313 =>
			array (
					'id' => 313,
					'cn' => '阿尔巴尼亚',
					'en' => 'Albania',
			),
			314 =>
			array (
					'id' => 314,
					'cn' => '安道尔',
					'en' => 'Andorra',
			),
			315 =>
			array (
					'id' => 315,
					'cn' => '奥地利',
					'en' => 'Austria',
			),
			316 =>
			array (
					'id' => 316,
					'cn' => '保加利亚',
					'en' => 'Bulgaria',
			),
			318 =>
			array (
					'id' => 318,
					'cn' => '芬兰',
					'en' => 'Finland',
			),
			320 =>
			array (
					'id' => 320,
					'cn' => '直布罗陀',
					'en' => 'Gibraltar',
			),
			321 =>
			array (
					'id' => 321,
					'cn' => '匈牙利',
					'en' => 'Hungary',
			),
			322 =>
			array (
					'id' => 322,
					'cn' => '冰岛',
					'en' => 'Iceland',
			),
			323 =>
			array (
					'id' => 323,
					'cn' => '列支敦士登',
					'en' => 'Liechtenstein',
			),
			324 =>
			array (
					'id' => 324,
					'cn' => '马耳他',
					'en' => 'Malta',
			),
			325 =>
			array (
					'id' => 325,
					'cn' => '摩纳哥',
					'en' => 'Monaco',
			),
			326 =>
			array (
					'id' => 326,
					'cn' => '挪威',
					'en' => 'Norway',
			),
			327 =>
			array (
					'id' => 327,
					'cn' => '波兰',
					'en' => 'Poland',
			),
			328 =>
			array (
					'id' => 328,
					'cn' => '罗马尼亚',
					'en' => 'Romania',
			),
			329 =>
			array (
					'id' => 329,
					'cn' => '圣马力诺',
					'en' => 'San Marino',
			),
			330 =>
			array (
					'id' => 330,
					'cn' => '瑞典',
					'en' => 'Sweden',
			),
			331 =>
			array (
					'id' => 331,
					'cn' => '瑞士',
					'en' => 'Switzerland',
			),
			334 =>
			array (
					'id' => 334,
					'cn' => '爱沙尼亚',
					'en' => 'Estonia',
			),
			335 =>
			array (
					'id' => 335,
					'cn' => '拉脱维亚',
					'en' => 'Latvia',
			),
			336 =>
			array (
					'id' => 336,
					'cn' => '立陶宛',
					'en' => 'Lithuania',
			),
			337 =>
			array (
					'id' => 337,
					'cn' => '格鲁吉亚',
					'en' => 'Georgia',
			),
			338 =>
			array (
					'id' => 338,
					'cn' => '亚美尼亚',
					'en' => 'Armenia',
			),
			339 =>
			array (
					'id' => 339,
					'cn' => '阿塞拜疆',
					'en' => 'Azerbaijan',
			),
			340 =>
			array (
					'id' => 340,
					'cn' => '白俄罗斯',
					'en' => 'Byelorussia',
			),
			343 =>
			array (
					'id' => 343,
					'cn' => '摩尔多瓦',
					'en' => 'Moldavia',
			),
			344 =>
			array (
					'id' => 344,
					'cn' => '俄罗斯联邦',
					'en' => 'Russia',
			),
			347 =>
			array (
					'id' => 347,
					'cn' => '乌克兰',
					'en' => 'Ukraine',
			),
			349 =>
			array (
					'id' => 349,
					'cn' => '南斯拉夫联盟共和国',
					'en' => 'Yugoslavia FR',
			),
			350 =>
			array (
					'id' => 350,
					'cn' => '斯洛文尼亚共和国',
					'en' => 'Slovenia Rep',
			),
			351 =>
			array (
					'id' => 351,
					'cn' => '克罗地亚共和国',
					'en' => 'Croatia Rep',
			),
			352 =>
			array (
					'id' => 352,
					'cn' => '捷克共和国',
					'en' => 'Czech Rep',
			),
			353 =>
			array (
					'id' => 353,
					'cn' => '斯洛伐克共和国',
					'en' => 'Slovak Rep',
			),
			354 =>
			array (
					'id' => 354,
					'cn' => '马其顿共和国',
					'en' => 'Macedonia Rep',
			),
			355 =>
			array (
					'id' => 355,
					'cn' => '波斯尼亚-黑塞哥维那共和',
					'en' => 'Bosnia&Hercegovina',
			),
			399 =>
			array (
					'id' => 399,
					'cn' => '欧洲其他国家(地区)',
					'en' => 'Oth. Eur. nes',
			),
			400 =>
			array (
					'id' => 400,
					'cn' => '拉丁美洲',
					'en' => 'Latin America',
			),
			401 =>
			array (
					'id' => 401,
					'cn' => '安提瓜和巴布达',
					'en' => 'Antigua & Barbuda',
			),
			402 =>
			array (
					'id' => 402,
					'cn' => '阿根廷',
					'en' => 'Argentina',
			),
			403 =>
			array (
					'id' => 403,
					'cn' => '阿鲁偷?',
					'en' => 'Aruba',
			),
			404 =>
			array (
					'id' => 404,
					'cn' => '巴哈马',
					'en' => 'Bahamas',
			),
			405 =>
			array (
					'id' => 405,
					'cn' => '巴巴多斯',
					'en' => 'Barbados',
			),
			406 =>
			array (
					'id' => 406,
					'cn' => '伯利兹',
					'en' => 'Belize',
			),
			408 =>
			array (
					'id' => 408,
					'cn' => '多民族玻利维亚国',
					'en' => 'Estado plurinacionalde',
			),
			409 =>
			array (
					'id' => 409,
					'cn' => '博内尔',
					'en' => 'Bonaire',
			),
			410 =>
			array (
					'id' => 410,
					'cn' => '巴西',
					'en' => 'Brazil',
			),
			411 =>
			array (
					'id' => 411,
					'cn' => '开曼群岛',
					'en' => 'Cayman Is',
			),
			412 =>
			array (
					'id' => 412,
					'cn' => '智利',
					'en' => 'Chile',
			),
			413 =>
			array (
					'id' => 413,
					'cn' => '哥伦比亚',
					'en' => 'Colombia',
			),
			414 =>
			array (
					'id' => 414,
					'cn' => '多米尼加',
					'en' => 'Dominica',
			),
			415 =>
			array (
					'id' => 415,
					'cn' => '哥斯达黎加',
					'en' => 'Costa Rica',
			),
			416 =>
			array (
					'id' => 416,
					'cn' => '古巴',
					'en' => 'Cuba',
			),
			417 =>
			array (
					'id' => 417,
					'cn' => '库腊索岛',
					'en' => 'Curacao',
			),
			418 =>
			array (
					'id' => 418,
					'cn' => '多米尼加',
					'en' => 'Dominican Rep.',
			),
			419 =>
			array (
					'id' => 419,
					'cn' => '厄瓜多尔',
					'en' => 'Ecuador',
			),
			420 =>
			array (
					'id' => 420,
					'cn' => '法属圭亚那',
					'en' => 'French Guyana',
			),
			421 =>
			array (
					'id' => 421,
					'cn' => '格林纳达',
					'en' => 'Grenada',
			),
			422 =>
			array (
					'id' => 422,
					'cn' => '瓜德罗普',
					'en' => 'Guadeloupe',
			),
			423 =>
			array (
					'id' => 423,
					'cn' => '危地马拉',
					'en' => 'Guatemala',
			),
			424 =>
			array (
					'id' => 424,
					'cn' => '圭亚那',
					'en' => 'Guyana',
			),
			425 =>
			array (
					'id' => 425,
					'cn' => '海地',
					'en' => 'Haiti',
			),
			426 =>
			array (
					'id' => 426,
					'cn' => '洪都拉斯',
					'en' => 'Honduras',
			),
			427 =>
			array (
					'id' => 427,
					'cn' => '牙买加',
					'en' => 'Jamaica',
			),
			428 =>
			array (
					'id' => 428,
					'cn' => '马提尼克',
					'en' => 'Martinique',
			),
			429 =>
			array (
					'id' => 429,
					'cn' => '墨西哥',
					'en' => 'Mexico',
			),
			430 =>
			array (
					'id' => 430,
					'cn' => '蒙特塞拉特',
					'en' => 'Montserrat',
			),
			431 =>
			array (
					'id' => 431,
					'cn' => '尼加拉瓜',
					'en' => 'Nicaragua',
			),
			432 =>
			array (
					'id' => 432,
					'cn' => '巴拿马',
					'en' => 'Panama',
			),
			433 =>
			array (
					'id' => 433,
					'cn' => '巴拉圭',
					'en' => 'Paraguay',
			),
			434 =>
			array (
					'id' => 434,
					'cn' => '秘鲁',
					'en' => 'Peru',
			),
			435 =>
			array (
					'id' => 435,
					'cn' => '波多黎各',
					'en' => 'Puerto Rico',
			),
			436 =>
			array (
					'id' => 436,
					'cn' => '萨巴',
					'en' => 'Saba',
			),
			437 =>
			array (
					'id' => 437,
					'cn' => '圣卢西亚',
					'en' => 'Saint Lucia',
			),
			438 =>
			array (
					'id' => 438,
					'cn' => '圣马丁岛',
					'en' => 'Saint Martin Is',
			),
			439 =>
			array (
					'id' => 439,
					'cn' => '圣文森特和格林纳丁斯',
					'en' => 'Saint Vincent & Grenadines',
			),
			440 =>
			array (
					'id' => 440,
					'cn' => '萨尔瓦多',
					'en' => 'El Salvador',
			),
			441 =>
			array (
					'id' => 441,
					'cn' => '苏里南',
					'en' => 'Suriname',
			),
			442 =>
			array (
					'id' => 442,
					'cn' => '特立尼达和多巴哥',
					'en' => 'Trinidad & Tobago',
			),
			443 =>
			array (
					'id' => 443,
					'cn' => '特克斯和凯科斯群岛',
					'en' => 'Turks & Caicos Is',
			),
			444 =>
			array (
					'id' => 444,
					'cn' => '乌拉圭',
					'en' => 'Uruguay',
			),
			445 =>
			array (
					'id' => 445,
					'cn' => '委内瑞拉',
					'en' => 'Venezuela',
			),
			446 =>
			array (
					'id' => 446,
					'cn' => '英属维尔京群岛',
					'en' => 'Br. Virgin Is',
			),
			447 =>
			array (
					'id' => 447,
					'cn' => '圣其茨-尼维斯',
					'en' => 'St. Kitts-Nevis',
			),
			448 =>
			array (
					'id' => 448,
					'cn' => '圣皮埃尔和密克隆',
					'en' => 'Saint,Pierre and Miquelon',
			),
			449 =>
			array (
					'id' => 449,
					'cn' => '荷属安地列斯',
					'en' => 'Netherlands Antilles',
			),
			499 =>
			array (
					'id' => 499,
					'cn' => '拉丁美洲其他国家(地区)',
					'en' => 'Oth. L.Amer. nes',
			),
			501 =>
			array (
					'id' => 501,
					'cn' => '加拿大',
					'en' => 'Canada',
			),
			502 =>
			array (
					'id' => 502,
					'cn' => '美国',
					'en' => 'United States',
			),
			503 =>
			array (
					'id' => 503,
					'cn' => '格陵兰',
					'en' => 'Greenland',
			),
			504 =>
			array (
					'id' => 504,
					'cn' => '百慕大',
					'en' => 'Bermuda',
			),
			599 =>
			array (
					'id' => 599,
					'cn' => '北美洲其他国家(地区)',
					'en' => 'Oth. N.Amer. nes',
			),
			600 =>
			array (
					'id' => 600,
					'cn' => '大洋洲',
					'en' => 'Oceania',
			),
			601 =>
			array (
					'id' => 601,
					'cn' => '澳大利亚',
					'en' => 'Australia',
			),
			602 =>
			array (
					'id' => 602,
					'cn' => '库克群岛',
					'en' => 'Cook Is',
			),
			603 =>
			array (
					'id' => 603,
					'cn' => '斐济',
					'en' => 'Fiji',
			),
			604 =>
			array (
					'id' => 604,
					'cn' => '盖比群岛',
					'en' => 'Gambier Is',
			),
			605 =>
			array (
					'id' => 605,
					'cn' => '马克萨斯群岛',
					'en' => 'Marquesas Is',
			),
			606 =>
			array (
					'id' => 606,
					'cn' => '瑙鲁',
					'en' => 'Nauru',
			),
			607 =>
			array (
					'id' => 607,
					'cn' => '新喀里多尼亚',
					'en' => 'New Caledonia',
			),
			608 =>
			array (
					'id' => 608,
					'cn' => '瓦努阿图',
					'en' => 'Vanuatu',
			),
			609 =>
			array (
					'id' => 609,
					'cn' => '新西兰',
					'en' => 'New Zealand',
			),
			610 =>
			array (
					'id' => 610,
					'cn' => '诺福克岛',
					'en' => 'Norfolk Is',
			),
			611 =>
			array (
					'id' => 611,
					'cn' => '巴布亚新几内亚',
					'en' => 'Papua New Guinea',
			),
			612 =>
			array (
					'id' => 612,
					'cn' => '社会群岛',
					'en' => 'Society Is',
			),
			613 =>
			array (
					'id' => 613,
					'cn' => '所罗门群岛',
					'en' => 'Solomon Is',
			),
			614 =>
			array (
					'id' => 614,
					'cn' => '汤加',
					'en' => 'Tonga',
			),
			615 =>
			array (
					'id' => 615,
					'cn' => '土阿莫土群岛',
					'en' => 'Tuamotu Is',
			),
			616 =>
			array (
					'id' => 616,
					'cn' => '土布艾群岛',
					'en' => 'Tubai Is',
			),
			617 =>
			array (
					'id' => 617,
					'cn' => '萨摩亚',
					'en' => 'Samoa',
			),
			618 =>
			array (
					'id' => 618,
					'cn' => '基里巴斯',
					'en' => 'Kiribati',
			),
			619 =>
			array (
					'id' => 619,
					'cn' => '图瓦卢',
					'en' => 'Tuvalu',
			),
			620 =>
			array (
					'id' => 620,
					'cn' => '密克罗尼西亚联邦',
					'en' => 'Micronesia Fs',
			),
			621 =>
			array (
					'id' => 621,
					'cn' => '马绍尔群岛',
					'en' => 'Marshall Is Rep',
			),
			622 =>
			array (
					'id' => 622,
					'cn' => '帕劳',
					'en' => 'Palau',
			),
			623 =>
			array (
					'id' => 623,
					'cn' => '法属波利尼西亚',
					'en' => 'French Polynesia',
			),
			625 =>
			array (
					'id' => 625,
					'cn' => '瓦利斯和浮图纳',
					'en' => 'Wallis and Futuna',
			),
			699 =>
			array (
					'id' => 699,
					'cn' => '大洋洲其他国家(地区)',
					'en' => 'Oth. Ocean. nes',
			),
			701 =>
			array (
					'id' => 701,
					'cn' => '国(地)别不详的',
					'en' => 'Countries(reg.) unknown',
			),
			702 =>
			array (
					'id' => 702,
					'cn' => '联合国际及机构和国际组织',
					'en' => 'UN and other interational',
			),
			999 =>
			array (
					'id' => 999,
					'cn' => '中性包装原产国别',
					'en' => 'Countries of Neutral package',
			),
	);
}