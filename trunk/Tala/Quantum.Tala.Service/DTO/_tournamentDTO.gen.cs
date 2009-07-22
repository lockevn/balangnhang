using System;
using System.Collections;
using System.Xml.Serialization;

using GURUCORE.Framework.Core.Data;

namespace Quantum.Tala.Service.DTO
{
	/// <summary>
	/// Summary for tournamentDTO.
	/// </summary>
	[Serializable]
	[System.Diagnostics.DebuggerStepThrough]
	[PersistenceClass("game_tournament","id",true)]
	 public sealed partial class tournamentDTO : DTOBase
	{
		public const string ID_FLD = "id";
		public const string NAME_FLD = "name";
		public const string DESC_FLD = "desc";
		public const string STARTTIME_FLD = "starttime";
		public const string ENDTIME_FLD = "endtime";
		public const string MINREQUIREDPLAYER_FLD = "minrequiredplayer";
		public const string ISSTART_FLD = "isstart";
		public const string ISENABLED_FLD = "isenabled";
		public const string STARTUPPOINT_FLD = "startuppoint";
		public const string ENROLLFEE_FLD = "enrollfee";
		public const string NUMOFVAN_FLD = "numofvan";
		public const string ADMINCREATOR_FLD = "admincreator";
		public const string TYPE_FLD = "type";

		private int m_nid;
		private string m_sname;
		private string m_sdesc;
		private DateTime m_dtstarttime;
		private DateTime m_dtendtime;
		private int m_nminrequiredplayer;
		private bool m_bisstart;
		private bool m_bisenabled;
		private int m_nstartuppoint;
		private int m_nenrollfee;
		private int m_nnumofvan;
		private string m_sadmincreator;
		private int m_ntype;

		public tournamentDTO()
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
		
		[PersistenceProperty("name",false,false)]
		public string name
		{
			get
			{
				return m_sname;
			}
			set
			{
				m_htIsNull["name"] = false;
				m_sname = value;
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
		
		[PersistenceProperty("starttime",false,false)]
		public DateTime starttime
		{
			get
			{
				return m_dtstarttime;
			}
			set
			{
				m_htIsNull["starttime"] = false;
				m_dtstarttime = value;
			}
		}
		
		[PersistenceProperty("endtime",false,false)]
		public DateTime endtime
		{
			get
			{
				return m_dtendtime;
			}
			set
			{
				m_htIsNull["endtime"] = false;
				m_dtendtime = value;
			}
		}
		
		[PersistenceProperty("minrequiredplayer",false,false)]
		public int minrequiredplayer
		{
			get
			{
				return m_nminrequiredplayer;
			}
			set
			{
				m_htIsNull["minrequiredplayer"] = false;
				m_nminrequiredplayer = value;
			}
		}
		
		[PersistenceProperty("isstart",false,false)]
		public bool isstart
		{
			get
			{
				return m_bisstart;
			}
			set
			{
				m_htIsNull["isstart"] = false;
				m_bisstart = value;
			}
		}
		
		[PersistenceProperty("isenabled",false,false)]
		public bool isenabled
		{
			get
			{
				return m_bisenabled;
			}
			set
			{
				m_htIsNull["isenabled"] = false;
				m_bisenabled = value;
			}
		}
		
		[PersistenceProperty("startuppoint",false,false)]
		public int startuppoint
		{
			get
			{
				return m_nstartuppoint;
			}
			set
			{
				m_htIsNull["startuppoint"] = false;
				m_nstartuppoint = value;
			}
		}
		
		[PersistenceProperty("enrollfee",false,false)]
		public int enrollfee
		{
			get
			{
				return m_nenrollfee;
			}
			set
			{
				m_htIsNull["enrollfee"] = false;
				m_nenrollfee = value;
			}
		}
		
		[PersistenceProperty("numofvan",false,false)]
		public int numofvan
		{
			get
			{
				return m_nnumofvan;
			}
			set
			{
				m_htIsNull["numofvan"] = false;
				m_nnumofvan = value;
			}
		}
		
		[PersistenceProperty("admincreator",false,false)]
		public string admincreator
		{
			get
			{
				return m_sadmincreator;
			}
			set
			{
				m_htIsNull["admincreator"] = false;
				m_sadmincreator = value;
			}
		}
		
		[PersistenceProperty("type",false,false)]
		public int type
		{
			get
			{
				return m_ntype;
			}
			set
			{
				m_htIsNull["type"] = false;
				m_ntype = value;
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
