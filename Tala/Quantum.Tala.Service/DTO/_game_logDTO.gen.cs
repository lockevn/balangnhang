using System;
using System.Collections;
using System.Xml.Serialization;

using GURUCORE.Framework.Core.Data;

namespace Quantum.Tala.Service.DTO
{
	/// <summary>
	/// Summary for game_logDTO.
	/// </summary>
	[Serializable]
	[System.Diagnostics.DebuggerStepThrough]
	[PersistenceClass("playing_game_log","id",true)]
	 public sealed partial class game_logDTO : DTOBase
	{
		public const string ID_FLD = "id";
		public const string SOIID_FLD = "soiid";
		public const string VANID_FLD = "vanid";
		public const string U_FLD = "u";
		public const string ACTION_FLD = "action";
		public const string DESC_FLD = "desc";
		public const string DT_FLD = "dt";

		private int m_nid;
		private int m_nsoiid;
		private int m_nvanid;
		private string m_su;
		private string m_saction;
		private string m_sdesc;
		private object m_odt;

		public game_logDTO()
		{
		}
		
		[PersistenceProperty("id",true,true)]
		public int id
		{
			get
			{
				return m_nid;
			}
			set
			{
				m_htIsNull["id"] = false;
				m_nid = value;
			}
		}
		
		[PersistenceProperty("soiid",false,false)]
		public int soiid
		{
			get
			{
				return m_nsoiid;
			}
			set
			{
				m_htIsNull["soiid"] = false;
				m_nsoiid = value;
			}
		}
		
		[PersistenceProperty("vanid",false,false)]
		public int vanid
		{
			get
			{
				return m_nvanid;
			}
			set
			{
				m_htIsNull["vanid"] = false;
				m_nvanid = value;
			}
		}
		
		[PersistenceProperty("u",false,false)]
		public string u
		{
			get
			{
				return m_su;
			}
			set
			{
				m_htIsNull["u"] = false;
				m_su = value;
			}
		}
		
		[PersistenceProperty("action",false,false)]
		public string action
		{
			get
			{
				return m_saction;
			}
			set
			{
				m_htIsNull["action"] = false;
				m_saction = value;
			}
		}
		
		[PersistenceProperty("desc",false,false)]
		public string desc
		{
			get
			{
				return m_sdesc;
			}
			set
			{
				m_htIsNull["desc"] = false;
				m_sdesc = value;
			}
		}
		
		[PersistenceProperty("dt",false,false)]
		public object dt
		{
			get
			{
				return m_odt;
			}
			set
			{
				m_htIsNull["dt"] = false;
				m_odt = value;
			}
		}

		public string IsNullSerializedString
		{
			get
			{
				return GetIsNullSerializedString();
			}
			set
			{
				SetIsNullSerializedString(value);
			}
		}
	}
}
