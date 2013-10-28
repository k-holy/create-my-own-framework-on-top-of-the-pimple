Create my own framework on top of the Pimple
=================================================

[![Build Status](https://travis-ci.org/k-holy/create-my-own-framework-on-top-of-the-pimple.png?branch=master)](https://travis-ci.org/k-holy/create-my-own-framework-on-top-of-the-pimple)

Dependency Injection Container [Pimple](https://github.com/fabpot/Pimple) を拡張して作成した小さなアプリケーションクラスを使って、
テスト用のWebアプリケーションを作成しながら、自分がフレームワークに求めるものを洗い出そうという試み。

ブログのシリーズ記事 [Create my own framework on top of the Pimple](http://k-holy.hatenablog.com/category/CMOFW) のリポジトリです。

src/Acme 以下にはアプリケーションで利用するクラスとそのテストケースがありますが、ある程度仕様が固まったと判断したものは、
その時点で別プロジェクトに独立させていきます。

以下がこのリポジトリで書いたコードを元に派生したプロジェクトです。

* [Volcanus_Configuration](https://github.com/k-holy/volcanus-configuration) 設定値を管理するためのクラス
* [Volcanus_Database](https://github.com/k-holy/volcanus-database) PDOを利用したデータベース抽象化ライブラリ
* [Volcanus_TemplateRenderer](https://github.com/k-holy/volcanus-template-renderer) 各種テンプレートエンジンを共通のインタフェースで利用するライブラリ

以下のライブラリにも、ここで発生した要求が仕様として取り込まれています。

* [Volcanus_Routing](https://github.com/k-holy/volcanus-routing) ページコントローラパターンで「きれいなURI」を実現するためのライブラリ
* [Volcanus_Error](https://github.com/k-holy/volcanus-error) エラーおよび例外処理用ライブラリ

このリポジトリは予告なく削除する可能性があります。
